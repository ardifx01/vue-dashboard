<template>
  <div>
    <VRow>
      <VCol cols="12">
        <VCard>
          <VCardHeader>
            <VCardTitle>Roles & Permissions</VCardTitle>
            <VSpacer />
            <VBtn
              color="primary"
              @click="openAddDialog"
            >
              <VIcon
                start
                icon="tabler-plus"
              />
              Add Role
            </VBtn>
          </VCardHeader>

          <VCardText>
            <!-- Roles Grid -->
            <VRow>
              <VCol
                v-for="role in roles"
                :key="role.id"
                cols="12"
                md="6"
                lg="4"
              >
                <VCard
                  :color="getRoleColor(role.name)"
                  variant="tonal"
                  class="role-card"
                >
                  <VCardText>
                    <div class="d-flex align-center justify-space-between mb-4">
                      <div>
                        <h5 class="text-h5 font-weight-bold text-capitalize">
                          {{ role.name }}
                        </h5>
                        <p class="text-caption mb-0">
                          {{ role.permissions?.length || 0 }} permissions
                        </p>
                      </div>
                      <VAvatar
                        :color="getRoleColor(role.name)"
                        size="40"
                      >
                        <VIcon :icon="getRoleIcon(role.name)" />
                      </VAvatar>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-4">
                      <VChip
                        v-for="permission in role.permissions?.slice(0, 3)"
                        :key="permission.id"
                        size="small"
                        class="me-1 mb-1"
                        variant="outlined"
                      >
                        {{ permission.name }}
                      </VChip>
                      <VChip
                        v-if="role.permissions?.length > 3"
                        size="small"
                        variant="outlined"
                        class="me-1 mb-1"
                      >
                        +{{ role.permissions.length - 3 }} more
                      </VChip>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                      <VBtn
                        size="small"
                        variant="outlined"
                        @click="editRole(role)"
                      >
                        <VIcon
                          start
                          icon="tabler-edit"
                        />
                        Edit
                      </VBtn>
                      <VBtn
                        v-if="!isDefaultRole(role.name)"
                        size="small"
                        color="error"
                        variant="outlined"
                        @click="deleteRole(role)"
                      >
                        <VIcon
                          start
                          icon="tabler-trash"
                        />
                        Delete
                      </VBtn>
                    </div>
                  </VCardText>
                </VCard>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Add/Edit Role Dialog -->
    <VDialog
      v-model="dialog"
      max-width="800px"
    >
      <VCard>
        <VCardHeader>
          <VCardTitle>
            {{ editMode ? 'Edit Role' : 'Add New Role' }}
          </VCardTitle>
        </VCardHeader>

        <VCardText>
          <VForm
            ref="form"
            @submit.prevent="saveRole"
          >
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="formData.name"
                  label="Role Name"
                  :rules="[rules.required]"
                  required
                />
              </VCol>

              <VCol cols="12">
                <h6 class="text-h6 mb-4">
                  Permissions
                </h6>
                <VRow>
                  <VCol
                    v-for="permission in availablePermissions"
                    :key="permission.id"
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <VCheckbox
                      v-model="formData.permissions"
                      :value="permission.name"
                      :label="permission.name"
                      density="compact"
                    />
                  </VCol>
                </VRow>
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
            @click="saveRole"
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
          Are you sure you want to delete role "{{ selectedRole?.name }}"?
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

const roles = ref([])
const availablePermissions = ref([])
const selectedRole = ref(null)
const form = ref()

const formData = reactive({
  name: '',
  permissions: []
})

// Validation rules
const rules = {
  required: v => !!v || 'This field is required'
}

// Methods
const fetchRoles = async () => {
  loading.value = true
  try {
    const response = await $fetch('/api/roles')
    roles.value = response.roles
  } catch (error) {
    console.error('Error fetching roles:', error)
  } finally {
    loading.value = false
  }
}

const fetchPermissions = async () => {
  try {
    const response = await $fetch('/api/permissions')
    availablePermissions.value = response.permissions
  } catch (error) {
    console.error('Error fetching permissions:', error)
  }
}

const openAddDialog = () => {
  editMode.value = false
  resetForm()
  dialog.value = true
}

const editRole = (role) => {
  editMode.value = true
  selectedRole.value = role
  formData.name = role.name
  formData.permissions = role.permissions?.map(p => p.name) || []
  dialog.value = true
}

const deleteRole = (role) => {
  selectedRole.value = role
  deleteDialog.value = true
}

const saveRole = async () => {
  const isValid = await form.value?.validate()
  if (!isValid.valid) return

  saving.value = true
  try {
    if (editMode.value) {
      await $fetch(`/api/roles/${selectedRole.value.id}`, {
        method: 'PUT',
        body: formData
      })
    } else {
      await $fetch('/api/roles', {
        method: 'POST',
        body: formData
      })
    }
    
    closeDialog()
    fetchRoles()
  } catch (error) {
    console.error('Error saving role:', error)
  } finally {
    saving.value = false
  }
}

const confirmDelete = async () => {
  deleting.value = true
  try {
    await $fetch(`/api/roles/${selectedRole.value.id}`, {
      method: 'DELETE'
    })
    
    deleteDialog.value = false
    fetchRoles()
  } catch (error) {
    console.error('Error deleting role:', error)
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
  formData.permissions = []
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

const getRoleIcon = (roleName) => {
  const icons = {
    admin: 'tabler-crown',
    user: 'tabler-user',
    moderator: 'tabler-shield'
  }
  return icons[roleName] || 'tabler-key'
}

const isDefaultRole = (roleName) => {
  return ['admin', 'user'].includes(roleName)
}

// Lifecycle
onMounted(() => {
  fetchRoles()
  fetchPermissions()
})

// Page meta
definePageMeta({
  middleware: 'auth',
  layout: 'default'
})
</script>

<style scoped>
.role-card {
  transition: transform 0.2s;
}

.role-card:hover {
  transform: translateY(-2px);
}
</style>
