// This is required for Visual Studio Code to recognize
// imported .vue files
declare module '*.vue' {
  import { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}
