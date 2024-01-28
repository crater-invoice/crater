import { defineAsyncComponent } from 'vue'

export const defineGlobalComponents = (app) => {
  const components = import.meta.glob('./components/base/*.vue', { eager: true })

  Object.entries(components).forEach(([path, definition]) => {
    // Get name of component, based on filename
    // "./components/Fruits.vue" will become "Fruits"
    const componentName = path
      .split('/')
      .pop()
      .replace(/\.\w+$/, '')

    // Register component on this Vue instance
    app.component(componentName, definition.default)
  })

  const BaseTable = defineAsyncComponent(() =>
    import('./components/base/base-table/BaseTable.vue')
  )

  const BaseMultiselect = defineAsyncComponent(() =>
    import('./components/base-select/BaseMultiselect.vue')
  )

  const BaseEditor = defineAsyncComponent(() =>
    import('./components/base/base-editor/BaseEditor.vue')
  )

  app.component('BaseTable', BaseTable)
  app.component('BaseMultiselect', BaseMultiselect)
  app.component('BaseEditor', BaseEditor)
}
