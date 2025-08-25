<script setup>
import { useTheme } from 'vuetify'

const { global } = useTheme()

const authProviders = [
  {
    name: 'google',
    icon: 'tabler-brand-google-filled',
    color: '#dd4b39',
    colorInDark: '#db4437',
    href: '/auth/google'
  },
  {
    name: 'github',
    icon: 'tabler-brand-github-filled',
    color: '#272727',
    colorInDark: '#fff',
    href: '/auth/github'
  }
]

const loginWithProvider = (provider) => {
  window.location.href = provider.href
}
</script>

<template>
  <div class="d-flex justify-center flex-wrap gap-2">
    <VBtn
      v-for="provider in authProviders"
      :key="provider.name"
      variant="outlined"
      size="large"
      class="flex-fill"
      @click="loginWithProvider(provider)"
    >
      <VIcon
        :icon="provider.icon"
        :color="global.name.value === 'dark' ? provider.colorInDark : provider.color"
        class="me-2"
      />
      Continue with {{ provider.name.charAt(0).toUpperCase() + provider.name.slice(1) }}
    </VBtn>
  </div>
</template>
