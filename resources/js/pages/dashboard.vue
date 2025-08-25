<template>
  <div>
    <VRow>
      <!-- Welcome Card -->
      <VCol cols="12">
        <VCard>
          <VCardText>
            <div class="d-flex align-center">
              <VAvatar
                size="64"
                color="primary"
                class="me-4"
              >
                <VIcon
                  size="32"
                  icon="tabler-dashboard"
                />
              </VAvatar>
              <div>
                <h2 class="text-h4 font-weight-bold mb-1">
                  Welcome back, {{ currentUser?.name }}! 👋
                </h2>
                <p class="text-body-1 mb-0">
                  Manage your application from this dashboard
                </p>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <!-- Stats Cards -->
      <VCol
        cols="12"
        sm="6"
        md="3"
      >
        <VCard>
          <VCardText>
            <div class="d-flex align-center">
              <VAvatar
                color="primary"
                variant="tonal"
                class="me-3"
              >
                <VIcon icon="tabler-users" />
              </VAvatar>
              <div>
                <p class="text-caption mb-0">
                  Total Users
                </p>
                <h4 class="text-h4 font-weight-bold">
                  {{ stats.users || 0 }}
                </h4>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        sm="6"
        md="3"
      >
        <VCard>
          <VCardText>
            <div class="d-flex align-center">
              <VAvatar
                color="success"
                variant="tonal"
                class="me-3"
              >
                <VIcon icon="tabler-shield-check" />
              </VAvatar>
              <div>
                <p class="text-caption mb-0">
                  Active Roles
                </p>
                <h4 class="text-h4 font-weight-bold">
                  {{ stats.roles || 0 }}
                </h4>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        sm="6"
        md="3"
      >
        <VCard>
          <VCardText>
            <div class="d-flex align-center">
              <VAvatar
                color="info"
                variant="tonal"
                class="me-3"
              >
                <VIcon icon="tabler-key" />
              </VAvatar>
              <div>
                <p class="text-caption mb-0">
                  Permissions
                </p>
                <h4 class="text-h4 font-weight-bold">
                  {{ stats.permissions || 0 }}
                </h4>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        sm="6"
        md="3"
      >
        <VCard>
          <VCardText>
            <div class="d-flex align-center">
              <VAvatar
                color="warning"
                variant="tonal"
                class="me-3"
              >
                <VIcon icon="tabler-trending-up" />
              </VAvatar>
              <div>
                <p class="text-caption mb-0">
                  Growth
                </p>
                <h4 class="text-h4 font-weight-bold">
                  {{ stats.growth || 0 }}%
                </h4>
              </div>
            </div>
          </VCardText>
        </VCard>
      </VCol>

      <!-- Recent Activity -->
      <VCol cols="12" md="8">
        <VCard>
          <VCardHeader>
            <VCardTitle>Recent Activity</VCardTitle>
          </VCardHeader>
          <VCardText>
            <VList>
              <VListItem
                v-for="(activity, index) in recentActivity"
                :key="index"
                :prepend-icon="activity.icon"
                :title="activity.title"
                :subtitle="activity.time"
              >
                <template #prepend>
                  <VAvatar
                    :color="activity.color"
                    variant="tonal"
                    size="32"
                  >
                    <VIcon :icon="activity.icon" />
                  </VAvatar>
                </template>
              </VListItem>
            </VList>
          </VCardText>
        </VCard>
      </VCol>

      <!-- Quick Actions -->
      <VCol cols="12" md="4">
        <VCard>
          <VCardHeader>
            <VCardTitle>Quick Actions</VCardTitle>
          </VCardHeader>
          <VCardText>
            <VList>
              <VListItem
                to="/users"
                prepend-icon="tabler-users"
                title="Manage Users"
                subtitle="Add, edit, or remove users"
              />
              <VListItem
                to="/roles"
                prepend-icon="tabler-shield"
                title="Manage Roles"
                subtitle="Configure roles and permissions"
              />
            </VList>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { computed, onMounted, ref } from 'vue'

// Stores
const authStore = useAuthStore()

// Reactive data
const stats = ref({
  users: 0,
  roles: 0,
  permissions: 0,
  growth: 0
})

const recentActivity = ref([
  {
    icon: 'tabler-user-plus',
    title: 'New user registered',
    time: '2 minutes ago',
    color: 'primary'
  },
  {
    icon: 'tabler-shield-check',
    title: 'Role permissions updated',
    time: '1 hour ago',
    color: 'success'
  },
  {
    icon: 'tabler-login',
    title: 'User logged in via Google',
    time: '3 hours ago',
    color: 'info'
  },
  {
    icon: 'tabler-settings',
    title: 'System settings updated',
    time: '1 day ago',
    color: 'warning'
  }
])

// Computed
const currentUser = computed(() => authStore.user)

// Methods
const fetchStats = async () => {
  try {
    const response = await $fetch('/api/dashboard/stats')
    stats.value = response
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

// Lifecycle
onMounted(() => {
  fetchStats()
})

// Page meta
definePageMeta({
  middleware: 'auth',
  layout: 'default'
})
</script>
