<template>
  <BaseContentPlaceholders v-if="contentLoading">
    <BaseContentPlaceholdersBox
      :rounded="true"
      class="w-full"
      style="height: 200px"
    />
  </BaseContentPlaceholders>
  <div
    v-else
    class="
      box-border
      w-full
      text-sm
      leading-8
      text-left
      bg-white
      border border-gray-200
      rounded-md
      min-h-[200px]
      overflow-hidden
    "
  >
    <div v-if="editor" class="editor-content">
      <div class="flex justify-end p-2 border-b border-gray-200 md:hidden">
        <BaseDropdown width-class="w-48">
          <template #activator>
            <div
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                ml-2
                text-sm text-black
                bg-white
                rounded-sm
                md:h-9 md:w-9
              "
            >
              <dots-vertical-icon class="w-6 h-6 text-gray-600" />
            </div>
          </template>
          <div class="flex flex-wrap space-x-1">
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('bold') }"
              @click="editor.chain().focus().toggleBold().run()"
            >
              <bold-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('italic') }"
              @click="editor.chain().focus().toggleItalic().run()"
            >
              <italic-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('strike') }"
              @click="editor.chain().focus().toggleStrike().run()"
            >
              <strikethrough-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('code') }"
              @click="editor.chain().focus().toggleCode().run()"
            >
              <coding-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('paragraph') }"
              @click="editor.chain().focus().setParagraph().run()"
            >
              <paragraph-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{
                'bg-gray-200': editor.isActive('heading', { level: 1 }),
              }"
              @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
            >
              H1
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{
                'bg-gray-200': editor.isActive('heading', { level: 2 }),
              }"
              @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
            >
              H2
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{
                'bg-gray-200': editor.isActive('heading', { level: 3 }),
              }"
              @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
            >
              H3
            </span>

            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('bulletList') }"
              @click="editor.chain().focus().toggleBulletList().run()"
            >
              <list-ul-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('orderedList') }"
              @click="editor.chain().focus().toggleOrderedList().run()"
            >
              <list-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('blockquote') }"
              @click="editor.chain().focus().toggleBlockquote().run()"
            >
              <quote-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('codeBlock') }"
              @click="editor.chain().focus().toggleCodeBlock().run()"
            >
              <code-block-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('undo') }"
              @click="editor.chain().focus().undo().run()"
            >
              <undo-icon class="h-3 cursor-pointer fill-current" />
            </span>
            <span
              class="
                flex
                items-center
                justify-center
                w-6
                h-6
                rounded-sm
                cursor-pointer
                hover:bg-gray-100
              "
              :class="{ 'bg-gray-200': editor.isActive('redo') }"
              @click="editor.chain().focus().redo().run()"
            >
              <redo-icon class="h-3 cursor-pointer fill-current" />
            </span>
          </div>
        </BaseDropdown>
      </div>
      <div class="hidden p-2 border-b border-gray-200 md:flex">
        <div class="flex flex-wrap space-x-1">
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('bold') }"
            @click="editor.chain().focus().toggleBold().run()"
          >
            <bold-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('italic') }"
            @click="editor.chain().focus().toggleItalic().run()"
          >
            <italic-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('strike') }"
            @click="editor.chain().focus().toggleStrike().run()"
          >
            <strikethrough-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('code') }"
            @click="editor.chain().focus().toggleCode().run()"
          >
            <coding-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('paragraph') }"
            @click="editor.chain().focus().setParagraph().run()"
          >
            <paragraph-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('heading', { level: 1 }) }"
            @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
          >
            H1
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('heading', { level: 2 }) }"
            @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
          >
            H2
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('heading', { level: 3 }) }"
            @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
          >
            H3
          </span>

          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('bulletList') }"
            @click="editor.chain().focus().toggleBulletList().run()"
          >
            <list-ul-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('orderedList') }"
            @click="editor.chain().focus().toggleOrderedList().run()"
          >
            <list-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('blockquote') }"
            @click="editor.chain().focus().toggleBlockquote().run()"
          >
            <quote-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('codeBlock') }"
            @click="editor.chain().focus().toggleCodeBlock().run()"
          >
            <code-block-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('undo') }"
            @click="editor.chain().focus().undo().run()"
          >
            <undo-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive('redo') }"
            @click="editor.chain().focus().redo().run()"
          >
            <redo-icon class="h-3 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive({ textAlign: 'left' }) }"
            @click="editor.chain().focus().setTextAlign('left').run()"
          >
            <menu-alt2-icon class="h-5 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive({ textAlign: 'right' }) }"
            @click="editor.chain().focus().setTextAlign('right').run()"
          >
            <menu-alt3-icon class="h-5 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{
              'bg-gray-200': editor.isActive({ textAlign: 'justify' }),
            }"
            @click="editor.chain().focus().setTextAlign('justify').run()"
          >
            <menu-icon class="h-5 cursor-pointer fill-current" />
          </span>
          <span
            class="
              flex
              items-center
              justify-center
              w-6
              h-6
              rounded-sm
              cursor-pointer
              hover:bg-gray-100
            "
            :class="{ 'bg-gray-200': editor.isActive({ textAlign: 'center' }) }"
            @click="editor.chain().focus().setTextAlign('center').run()"
          >
            <menu-center-icon class="h-5 cursor-pointer fill-current" />
          </span>
        </div>
      </div>
      <editor-content
        :editor="editor"
        class="
          box-border
          relative
          w-full
          text-sm
          leading-8
          text-left
          editor__content
        "
      />
    </div>
  </div>
</template>

<script>
import { onUnmounted, watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import {
  DotsVerticalIcon,
  MenuAlt2Icon,
  MenuAlt3Icon,
  MenuIcon,
} from '@heroicons/vue/outline'
import TextAlign from '@tiptap/extension-text-align'

import {
  BoldIcon,
  CodingIcon,
  ItalicIcon,
  ListIcon,
  ListUlIcon,
  ParagraphIcon,
  QuoteIcon,
  StrikethroughIcon,
  UndoIcon,
  RedoIcon,
  CodeBlockIcon,
  MenuCenterIcon,
} from './icons/index.js'

export default {
  components: {
    EditorContent,
    BoldIcon,
    CodingIcon,
    ItalicIcon,
    ListIcon,
    ListUlIcon,
    ParagraphIcon,
    QuoteIcon,
    StrikethroughIcon,
    UndoIcon,
    RedoIcon,
    CodeBlockIcon,
    DotsVerticalIcon,
    MenuCenterIcon,
    MenuAlt2Icon,
    MenuAlt3Icon,
    MenuIcon,
  },

  props: {
    modelValue: {
      type: String,
      default: '',
    },
    contentLoading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const editor = useEditor({
      content: props.modelValue,
      extensions: [
        StarterKit,
        TextAlign.configure({
          types: ['heading', 'paragraph'],
          alignments: ['left', 'right', 'center', 'justify'],
        }),
      ],

      onUpdate: () => {
        emit('update:modelValue', editor.value.getHTML())
      },
    })

    watch(
      () => props.modelValue,
      (value) => {
        const isSame = editor.value.getHTML() === value

        if (isSame) {
          return
        }

        editor.value.commands.setContent(props.modelValue, false)
      }
    )

    onUnmounted(() => {
      setTimeout(() => {
        editor.value.destroy()
      }, 500)
    })

    return {
      editor,
    }
  },
}
</script>
<style lang="scss">
.ProseMirror {
  min-height: 200px;
  padding: 8px 12px;
  outline: none;
  @apply rounded-md rounded-tl-none rounded-tr-none border border-transparent;

  h1 {
    font-size: 2em;
    font-weight: bold;
  }

  h2 {
    font-size: 1.5em;
    font-weight: bold;
  }

  h3 {
    font-size: 1.17em;
    font-weight: bold;
  }

  ul {
    padding: 0 1rem;
    list-style: disc !important;
  }

  ol {
    padding: 0 1rem;
    list-style: auto !important;
  }

  blockquote {
    padding-left: 1rem;
    border-left: 2px solid rgba(#0d0d0d, 0.1);
  }

  code {
    background-color: rgba(97, 97, 97, 0.1);
    color: #616161;
    border-radius: 0.4rem;
    font-size: 0.9rem;
    padding: 0.1rem 0.3rem;
  }

  pre {
    background: #0d0d0d;
    color: #fff;
    font-family: 'JetBrainsMono', monospace;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;

    code {
      color: inherit;
      padding: 0;
      background: none;
      font-size: 0.8rem;
    }
  }
}

.ProseMirror:focus {
  @apply border border-primary-400 ring-primary-400;
}
</style>
