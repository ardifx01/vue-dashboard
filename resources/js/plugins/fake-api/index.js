import { setupWorker } from 'msw/browser'

// Handlers (excluding auth since we're using real Laravel API)
import { handlerAppBarSearch } from '@db/app-bar-search/index'
import { handlerDashboard } from '@db/dashboard/index'
import { handlerPagesDatatable } from '@db/pages/datatable/index'
import { handlerPagesFaq } from '@db/pages/faq/index'
import { handlerPagesHelpCenter } from '@db/pages/help-center/index'
import { handlerPagesProfile } from '@db/pages/profile/index'

const worker = setupWorker(...handlerPagesDatatable, ...handlerPagesHelpCenter, ...handlerPagesProfile, ...handlerPagesFaq, ...handlerAppBarSearch, ...handlerDashboard)

export default function () {
  // Only enable MSW in development for non-auth endpoints
  // In production, all API calls go to Laravel backend
  if (import.meta.env.DEV) {
    const workerUrl = `${import.meta.env.BASE_URL.replace(/build\/$/g, '') ?? '/'}mockServiceWorker.js`

    worker.start({
      serviceWorker: {
        url: workerUrl,
      },
      onUnhandledRequest: 'bypass',
      quiet: true, // Disable MSW logs for cleaner console
    })
    
    console.log('🎭 MSW enabled for development (non-auth endpoints only)')
  }
}
