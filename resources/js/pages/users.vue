<template>
  <div>
    <VRow>
      <VCol cols="12">
        <VCard>
          <VCardHeader>
            <VCardTitle>Users Management</VCardTitle>
            <VSpacer />
            <VBtn
              color="primary"
              @click="openAddDialog"
            >
              <VIcon
                start
                icon="tabler-plus"
              />
              Add User
            </VBtn>
          </VCardHeader>

          <VCardText>
            <!-- Search -->
            <VRow class="mb-4">
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="search"
                  placeholder="Search users..."
                  prepend-inner-icon="tabler-search"
                  clearable
                  @input="searchUsers"
                />
              </VCol>
            </VRow>

            <!-- Data Table -->
            <VDataTableServer
              v-model:items-per-page="itemsPerPage"
              v-model:page="page"
              :headers="headers"
              :items="users"
              :items-length="totalUsers"
              :loading="loading"
              :search="search"
              item-value="id"
              @update:options="fetchUsers"
            >
              <template #item.avatar="{ item }">
                <VAvatar
                  size="32"
                  :image="item.avatar"
                  :color="!item.avatar ? 'primary' : undefined"
                >
                  <span v-if="!item.avatar">{{ item.name?.charAt(0) }}</span>
                </VAvatar>
              </template>

              <template #item.roles="{ item }">
                <VChip
                  v-for="role in item.roles"
                  :key="role.id"
                  :color="getRoleColor(role.name)"
                  size="small"
                  class="me-2"
                >
                  {{ role.name }}
                </VChip>
              </template>

              <template #item.created_at="{ item }">
                {{ formatDate(item.created_at) }}
              </template>

              <template #item.actions="{ item }">
                <VBtn
                  icon
                  size="small"
                  color="primary"
                  variant="text"
                  @click="editUser(item)"
                >
                  <VIcon icon="tabler-edit" />
                </VBtn>
                <VBtn
                  icon
                  size="small"
                  color="error"
                  variant="text"
                  @click="deleteUser(item)"
                >
                  <VIcon icon="tabler-trash" />
                </VBtn>
              </template>
            </VDataTableServer>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Add/Edit User Dialog -->
    <VDialog
      v-model="dialog"
      max-width="600px"
    >
      <VCard>
        <VCardHeader>
          <VCardTitle>
            {{ editMode ? 'Edit User' : 'Add New User' }}
          </VCardTitle>
        </VCardHeader>

        <VCardText>
          <VForm
            ref="form"
            @submit.prevent="saveUser"
          >
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="formData.name"
                  label="Name"
                  :rules="[rules.required]"
                  required
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="formData.email"
                  label="Email"
                  type="email"
                  :rules="[rules.required, rules.email]"
                  required
                />
              </VCol>

              <VCol
                v-if="!editMode"
                cols="12"
              >
                <VTextField
                  v-model="formData.password"
                  label="Password"
                  type="password"
                  :rules="[rules.required, rules.min]"
                  required
                />
              </VCol>

              <VCol cols="12">
                <VSelect
                  v-model="formData.roles"
                  :items="availableRoles"
                  item-title="name"
                  item-value="name"
                  label="Roles"
                  multiple
                  chips
                />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>

        <VCardActions>
          <VSpacer />
          <VBtn
            variant="text"
            @click="closeDialog"
          >
            Cancel
          </VBtn>
          <VBtn
            color="primary"
            :loading="saving"
            @click="saveUser"
          >
            {{ editMode ? 'Update' : 'Create' }}
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- Delete Confirmation Dialog -->
    <VDialog
      v-model="deleteDialog"
      max-width="400px"
    >
      <VCard>
        <VCardHeader>
          <VCardTitle>Confirm Delete</VCardTitle>
        </VCardHeader>

        <VCardText>
          Are you sure you want to delete user "{{ selectedUser?.name }}"?
          This action cannot be undone.
        </VCardText>

        <VCardActions>
          <VSpacer />
          <VBtn
            variant="text"
            @click="deleteDialog = false"
          >
            Cancel
          </VBtn>
          <VBtn
            color="error"
            :loading="deleting"
            @click="confirmDelete"
          >
            Delete
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<script setup>
import { nextTick, onMounted, reactive, ref } from 'vue'

// Reactive data
const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const dialog = ref(false)
const deleteDialog = ref(false)
const editMode = ref(false)
const search = ref('')
const page = ref(1)
const itemsPerPage = ref(10)
const totalUsers = ref(0)

const users = ref([])
const availableRoles = ref([])
const selectedUser = ref(null)
const form = ref()

const formData = reactive({
  name: '',
  email: '',
  password: '',
  roles: []
})

// Table headers
const headers = [
  { title: 'Avatar', key: 'avatar', sortable: false },
  { title: 'Name', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Roles', key: 'roles', sortable: false },
  { title: 'Created', key: 'created_at' },
  { title: 'Actions', key: 'actions', sortable: false }
]

// Validation rules
const rules = {
  required: v => !!v || 'This field is required',
  email: v => /.+@.+\..+/.test(v) || 'Email must be valid',
  min: v => v.length >= 8 || 'Password must be at least 8 characters'
}

// Methods
const fetchUsers = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({
      page: page.value,
      per_page: itemsPerPage.value,
      search: search.value
    })
    
    const response = await $fetch(`/api/users?${params}`)
    users.value = response.data
    totalUsers.value = response.total
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const fetchRoles = async () => {
  try {
    const response = await $fetch('/api/roles')
    availableRoles.value = response.roles
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
}

const searchUsers = () => {
  page.value = 1
  fetchUsers()
}

const openAddDialog = () => {
  editMode.value = false
  resetForm()
  dialog.value = true
}

const editUser = (user) => {
  editMode.value = true
  selectedUser.value = user
  formData.name = user.name
  formData.email = user.email
  formData.roles = user.roles.map(role => role.name)
  dialog.value = true
}

const deleteUser = (user) => {
  selectedUser.value = user
  deleteDialog.value = true
}

const saveUser = async () => {
  const isValid = await form.value?.validate()
  if (!isValid.valid) return

  saving.value = true
  try {
    if (editMode.value) {
      await $fetch(`/api/users/${selectedUser.value.id}`, {
        method: 'PUT',
        body: {
          name: formData.name,
          email: formData.email,
          roles: formData.roles
        }
      })
    } else {
      await $fetch('/api/users', {
        method: 'POST',
        body: formData
      })
    }
    
    closeDialog()
    fetchUsers()
  } catch (error) {
    console.error('Error saving user:', error)
  } finally {
    saving.value = false
  }
}

const confirmDelete = async () => {
  deleting.value = true
  try {
    await $fetch(`/api/users/${selectedUser.value.id}`, {
      method: 'DELETE'
    })
    
    deleteDialog.value = false
    fetchUsers()
  } catch (error) {
    console.error('Error deleting user:', error)
  } finally {
    deleting.value = false
  }
}

const closeDialog = () => {
  dialog.value = false
  nextTick(() => {
    resetForm()
  })
}

const resetForm = () => {
  formData.name = ''
  formData.email = ''
  formData.password = ''
  formData.roles = []
  form.value?.resetValidation()
}

const getRoleColor = (roleName) => {
  const colors = {
    admin: 'error',
    user: 'primary',
    moderator: 'warning'
  }
  return colors[roleName] || 'secondary'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

// Lifecycle
onMounted(() => {
  fetchUsers()
  fetchRoles()
})

// Page meta
definePageMeta({
  middleware: 'auth',
  layout: 'default'
})
</script>
