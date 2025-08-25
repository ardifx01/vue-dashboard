import { setupWorker } from 'msw/browser'

// Handlers
import { handlerAppBarSearch } from '@db/app-bar-search/index'
import { handlerAuth } from '@db/auth/index'
import { handlerDashboard } from '@db/dashboard/index'
import { handlerPagesDatatable } from '@db/pages/datatable/index'
import { handlerPagesFaq } from '@db/pages/faq/index'
import { handlerPagesHelpCenter } from '@db/pages/help-center/index'
import { handlerPagesProfile } from '@db/pages/profile/index'

const worker = setupWorker(...handlerPagesDatatable, ...handlerPagesHelpCenter, ...handlerPagesProfile, ...handlerPagesFaq, ...handlerAppBarSearch, ...handlerAuth, ...handlerDashboard)
export default function () {
  const workerUrl = `${import.meta.env.BASE_URL.replace(/build\/$/g, '') ?? '/'}mockServiceWorker.js`

  worker.start({
    serviceWorker: {
      url: workerUrl,
    },
    onUnhandledRequest: 'bypass',
  })
}
