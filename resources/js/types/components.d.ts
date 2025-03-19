declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

declare module '@/components/ui/file-upload.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{
    modelValue: File | null
    accept?: string
    maxSize?: number
    class?: string
    currentFile?: string | null
    description?: string
  }, {}, any>
  export default component
}

declare module '@/layouts/app/AppSidebarLayout.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{
    breadcrumbs?: Array<{
      title: string
      href: string
    }>
  }, {}, any>
  export default component
}

declare module '@/components/ui/button/Button.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{
    type?: 'button' | 'submit' | 'reset'
    variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
    size?: 'default' | 'sm' | 'lg' | 'icon'
    disabled?: boolean
    class?: string
  }, {}, any>
  export default component
}

declare module '@/components/ui/input.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{
    type?: string
    modelValue: string
    name?: string
    id?: string
    placeholder?: string
    required?: boolean
    disabled?: boolean
    class?: string
  }, {}, any>
  export default component
}

declare module '@/components/ui/label.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{
    for?: string
    class?: string
  }, {}, any>
  export default component
}

declare module '@/components/ui/textarea.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{
    modelValue: string
    name?: string
    id?: string
    placeholder?: string
    required?: boolean
    disabled?: boolean
    class?: string
  }, {}, any>
  export default component
} 