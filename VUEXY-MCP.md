# Vuexy MCP (Model Context Protocol) Guide

## Overview
This guide provides AI agents with comprehensive information about the Vuexy Vue.js dashboard template structure and Laravel backend integration. The `example/` folder contains organized reference components, views, and backend patterns for building modern dashboard applications.

## Project Architecture

### Frontend Stack
- **Vue 3.4.21** - Composition API with modern reactive system
- **Vuetify 3.8.5** - Material Design component framework
- **Vite 7.1.3** - Fast build tool with HMR
- **Pinia 3.0.2** - State management solution
- **TypeScript Support** - Auto-generated types and router

### Backend Stack
- **Laravel 12** - PHP framework for robust backend APIs
- **MySQL Database** - Relational database with migrations
- **Laravel Debug Bar** - Debugging tool for development
- **API Routes** - RESTful endpoints for data exchange

## File Structure Reference

```
vue-dashboard/
├── example/                           # Reference examples for AI training
│   ├── frontend/                      # Vue.js components and views
│   │   ├── components/               # Reusable UI components
│   │   ├── layouts/                  # Layout templates
│   │   ├── pages/                    # Page-specific components
│   │   └── views/                    # Feature-complete views
│   └── backend/                      # Laravel patterns (to be created)
├── resources/js/                     # Active frontend code
│   ├── @core/                       # Core utilities and composables
│   ├── @layouts/                    # Layout system
│   ├── navigation/                  # Navigation configuration
│   ├── plugins/                     # Vue plugins setup
│   └── utils/                       # Helper functions
├── app/Http/Controllers/            # Laravel controllers
├── routes/                          # API and web routes
├── database/                        # Migrations and seeders
└── config/                          # Application configuration
```

## Component Categories

### 1. Dashboard Components (`example/frontend/views/dashboards/`)
- **Analytics Dashboard**: Data visualization components
- **CRM Dashboard**: Customer relationship management widgets
- **eCommerce Dashboard**: Sales and inventory components

**Key Components:**
- `AnalyticsEarningReportsWeeklyOverview.vue` - Chart-based earnings display
- `CrmActiveProject.vue` - Project status tracking
- `EcommerceOrder.vue` - Order management interface

### 2. Form Components (`example/frontend/views/demos/forms/`)
- **Form Elements**: Input fields, selects, checkboxes, radio buttons
- **Form Layout**: Multi-column, horizontal, vertical layouts
- **Form Validation**: Client-side validation patterns
- **Form Wizard**: Step-by-step form flows

**Key Components:**
- `DemoFormLayoutMultipleColumn.vue` - Multi-column form structure
- `DemoFormValidationValidationTypes.vue` - Validation examples
- `DemoFormWizardIconsBasic.vue` - Wizard with icon navigation

### 3. UI Components (`example/frontend/views/demos/components/`)
- **Alert**: Notification and alert systems
- **Avatar**: User profile displays
- **Badge**: Status indicators
- **Button**: Action triggers with various styles
- **Card**: Content containers
- **Chart**: Data visualization
- **Dialog**: Modal interactions
- **List**: Data listing components
- **Menu**: Navigation menus
- **Pagination**: Data pagination
- **Tabs**: Tabbed interfaces
- **Timeline**: Chronological displays

### 4. App Components (`example/frontend/views/apps/`)
- **Academy**: Learning management system
- **Calendar**: Event scheduling
- **Chat**: Real-time messaging
- **eCommerce**: Online store features
- **Email**: Email management
- **Invoice**: Billing system
- **Kanban**: Project management boards
- **Logistics**: Supply chain management
- **User Management**: User CRUD operations

### 5. Page Components (`example/frontend/pages/`)
- **Account Settings**: User profile management
- **Authentication**: Login/register flows
- **Cards**: Various card layouts
- **Charts**: Chart implementations
- **Help Center**: Documentation system
- **Typography**: Text styling
- **User Profile**: Profile displays

## Laravel Backend Patterns

### API Controller Structure
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Barryvdh\Debugbar\Facades\Debugbar;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        Debugbar::info('Dashboard stats requested');
        
        $stats = [
            'users' => \App\Models\User::count(),
            'orders' => 0, // Add your order model
            'revenue' => 0, // Add your revenue calculation
            'growth' => 12.5
        ];

        return response()->json($stats);
    }

    public function analytics(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        // Your analytics logic here
        $analytics = [
            'pageViews' => rand(1000, 5000),
            'visitors' => rand(100, 1000),
            'bounceRate' => rand(20, 80),
        ];
        
        $endTime = microtime(true);
        Debugbar::info('Analytics processed in ' . ($endTime - $startTime) . ' seconds');
        
        return response()->json($analytics);
    }
}
```

### API Routes (`routes/api.php`)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::prefix('dashboard')->group(function () {
    Route::get('/stats', [DashboardController::class, 'stats']);
    Route::get('/analytics', [DashboardController::class, 'analytics']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});
```

## Component Creation Guidelines

### 1. Vue Component Structure
```vue
<template>
  <VCard>
    <VCardText>
      <!-- Component content -->
    </VCardText>
  </VCard>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'

// Props
const props = defineProps({
  title: String,
  data: Array
})

// Reactive state
const loading = ref(false)
const items = ref([])

// Computed properties
const totalItems = computed(() => items.value.length)

// Lifecycle
onMounted(() => {
  fetchData()
})

// Methods
const fetchData = async () => {
  loading.value = true
  try {
    const response = await useApi('/api/dashboard/stats')
    items.value = response.data
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}
</script>
```

### 2. Vuetify Component Patterns

#### Cards
```vue
<VCard>
  <VCardTitle>{{ title }}</VCardTitle>
  <VCardText>{{ content }}</VCardText>
  <VCardActions>
    <VBtn variant="text">Action</VBtn>
  </VCardActions>
</VCard>
```

#### Forms
```vue
<VForm ref="form" @submit.prevent="handleSubmit">
  <VTextField
    v-model="form.name"
    label="Name"
    :rules="[rules.required]"
  />
  <VBtn type="submit" :loading="loading">Submit</VBtn>
</VForm>
```

#### Data Tables
```vue
<VDataTable
  :headers="headers"
  :items="items"
  :loading="loading"
  item-value="id"
>
  <template #item.actions="{ item }">
    <VBtn @click="editItem(item)">Edit</VBtn>
  </template>
</VDataTable>
```

## State Management with Pinia

### Store Structure
```javascript
import { defineStore } from 'pinia'

export const useDashboardStore = defineStore('dashboard', () => {
  // State
  const stats = ref({})
  const loading = ref(false)
  
  // Getters
  const totalUsers = computed(() => stats.value.users || 0)
  
  // Actions
  const fetchStats = async () => {
    loading.value = true
    try {
      const response = await $fetch('/api/dashboard/stats')
      stats.value = response
    } finally {
      loading.value = false
    }
  }
  
  return {
    stats,
    loading,
    totalUsers,
    fetchStats
  }
})
```

## Styling Guidelines

### Theme Configuration
The project uses Vuetify's theme system with custom colors:

```javascript
// plugins/vuetify.js
export default createVuetify({
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#9155FD',
          secondary: '#8A8D93',
          success: '#56CA00',
          info: '#16B1FF',
          warning: '#FFB400',
          error: '#FF4C51'
        }
      }
    }
  }
})
```

### SCSS Structure
```scss
// styles/styles.scss
@use '@core/scss/template/index';

// Custom styles
.dashboard-card {
  .v-card-title {
    background: rgb(var(--v-theme-primary));
    color: rgb(var(--v-theme-on-primary));
  }
}
```

## Best Practices

### 1. Component Development
- Use Composition API for new components
- Implement proper TypeScript types
- Follow Vue 3 best practices
- Use Vuetify components consistently

### 2. API Integration
- Use composables for API calls
- Implement proper error handling
- Add loading states
- Cache data when appropriate

### 3. State Management
- Use Pinia stores for shared state
- Keep components focused
- Implement proper reactivity

### 4. Performance
- Lazy load routes and components
- Use virtual scrolling for large lists
- Optimize images and assets
- Implement proper caching

## Reference Documentation

- **Vuexy Official Docs**: https://demos.pixinvent.com/vuexy-vuejs-admin-template/documentation/guide/
- **Vue 3 Documentation**: https://vuejs.org/
- **Vuetify Documentation**: https://vuetifyjs.com/
- **Laravel Documentation**: https://laravel.com/docs
- **Pinia Documentation**: https://pinia.vuejs.org/

## Development Workflow

### 1. Creating New Components
1. Check `example/frontend/` for similar components
2. Use existing patterns and structures
3. Follow Vuetify design principles
4. Implement responsive design
5. Add proper TypeScript types

### 2. Adding Backend APIs
1. Create controller in `app/Http/Controllers/`
2. Define routes in `routes/api.php`
3. Add Laravel Debug Bar logging
4. Implement proper validation
5. Return JSON responses

### 3. Integration
1. Create API composables
2. Connect frontend to backend
3. Handle loading and error states
4. Test functionality
5. Add documentation

This guide provides AI agents with the necessary context to build components using the Vuexy template patterns and Laravel backend integration.
