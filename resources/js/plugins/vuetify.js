import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

const vuetify = createVuetify({
  components,
  directives,
 theme: {
    defaultTheme: 'dark',
    themes: {
      light: {
        dark: false,
        colors: {
          background: '#c2ddeb', 
          surface: '#FFFFFF',    
          primary: '#075985',    
          secondary: '#64748B',  
          accent: '#0EA5E9',     
          error: '#E11D48',
          info: '#0284C7',
          success: '#10B981',
          warning: '#F59E0B',
        },
        variables: {
          'border-color': '#BAE6FD',
          'hover-opacity': 0.04,
          'focus-opacity': 0.10,
          'selected-opacity': 0.08,
          'activated-opacity': 0.12,
          'pressed-opacity': 0.12,
          'dragged-opacity': 0.08,
        },
      },
      dark: {
        dark: true,
        colors: {
          background: '#0F172A',
          surface: '#1E293B',
          primary: '#3B82F6',
          secondary: '#94A3B8',
          accent: '#60A5FA',
        },
      },
    },
  },
})

export default vuetify