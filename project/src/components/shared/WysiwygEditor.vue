<template>
  <div class="wysiwyg-editor-container">
    <Editor
      v-model="editorContent"
      :init="editorConfig"
      :disabled="readOnly"
      class="tinymce-editor"
      api-key="bhzhqrj5zy8hc6r6y0dlz9fu1kz0guzcw13szbgryoumiw4t"
      :output-format="'html'"
      @update:modelValue="updateContent"
    />

    <div v-if="error" class="editor-error">
      {{ error }}
    </div>
  </div>
</template>

<script>
import Editor from "@tinymce/tinymce-vue";

export default {
  name: "WysiwygEditor",
  components: {
    Editor,
  },
  props: {
    value: {
      type: String,
      default: "",
    },
    conferenceId: {
      type: String,
      required: true,
    },
    subpageId: {
      type: String,
      required: true,
    },
    readOnly: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      editorContent: this.value,
      error: null,
      editorConfig: {
        height: 400,
        menubar: true,
        plugins: [
          "advlist",
          "autolink",
          "lists",
          "link",
          "image",
          "charmap",
          "preview",
          "anchor",
          "searchreplace",
          "visualblocks",
          "code",
          "fullscreen",
          "insertdatetime",
          "media",
          "table",
          "code",
          "help",
          "wordcount",
        ],
        toolbar:
          "undo redo | blocks | " +
          "bold italic forecolor | alignleft aligncenter " +
          "alignright alignjustify | bullist numlist outdent indent | " +
          "removeformat | help",
        content_style:
          "body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }",
      },
    };
  },
  methods: {
    updateContent(newContent) {
      this.$emit("update:value", newContent);
    },
  },
  watch: {
    value(newValue) {
      if (newValue !== this.editorContent) {
        this.editorContent = newValue;
      }
    },
  },
};
</script>

<style scoped>
.wysiwyg-editor-container {
  position: relative;
  margin-bottom: var(--spacing-lg);
}

.editor-error {
  color: var(--color-error);
  margin-top: var(--spacing-sm);
  padding: var(--spacing-sm);
  background-color: rgba(244, 67, 54, 0.1);
  border-radius: var(--border-radius-sm);
}

.tinymce-editor {
  border: 2px solid var(--color-border);
  border-radius: 0.5rem;
  overflow: hidden;
}

.tinymce-editor :deep(.tox-tinymce) {
  border: none !important;
}
</style>
