<template>
  <div class="wysiwyg-editor-container">
    <QuillEditor
      v-model:content="editorContent"
      :toolbar="toolbarOptions"
      contentType="html"
      theme="snow"
      @update:content="updateContent"
      @textChange="handleTextChange"
      :modules="modules"
    />
    
    <div v-if="error" class="editor-error">
      {{ error }}
    </div>
    
    <div v-if="fileUploading" class="editor-uploading">
      Uploading file... Please wait.
    </div>
  </div>
</template>

<script>
import { QuillEditor } from '@vueup/vue-quill'
import 'quill/dist/quill.snow.css'
import ImageUploader from 'quill-image-uploader'
import { useSubpageStore } from '../../stores/subpage'
import { ref } from 'vue'

export default {
  name: 'WysiwygEditor',
  components: {
    QuillEditor
  },
  props: {
    value: {
      type: String,
      default: ''
    },
    conferenceId: {
      type: String,
      required: true
    },
    subpageId: {
      type: String,
      required: true
    },
    readOnly: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      editorContent: this.value,
      error: null,
      fileUploading: false,
      toolbarOptions: [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'script': 'sub' }, { 'script': 'super' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['clean'],
        ['link', 'image'],
        ['file']
      ],
      modules: {
        imageUploader: {
          upload: this.handleImageUpload
        }
      }
    }
  },
  methods: {
    updateContent(newContent) {
      this.$emit('update:value', newContent)
    },
    handleTextChange() {
      this.$emit('text-change', this.editorContent)
    },
    async handleImageUpload(file) {
      const subpageStore = useSubpageStore()
      
      if (file.type.indexOf('image/') !== 0) {
        this.error = 'Only image files are allowed for image upload'
        return
      }
      
      this.fileUploading = true
      this.error = null
      
      try {
        const fileData = await subpageStore.uploadFile(
          this.conferenceId,
          this.subpageId,
          file
        )
        
        if (!fileData) {
          throw new Error('File upload failed')
        }
        
        return fileData.url
      } catch (error) {
        this.error = error.message || 'Failed to upload image'
        console.error('Error uploading image:', error)
        return null
      } finally {
        this.fileUploading = false
      }
    }
  },
  watch: {
    value(newValue) {
      if (newValue !== this.editorContent) {
        this.editorContent = newValue
      }
    }
  }
}
</script>

<style>
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

.editor-uploading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: rgba(0, 0, 0, 0.7);
  color: white;
  padding: var(--spacing-md) var(--spacing-lg);
  border-radius: var(--border-radius-md);
  z-index: 100;
}

.ql-snow .ql-picker.ql-header .ql-picker-label::before,
.ql-snow .ql-picker.ql-header .ql-picker-item::before {
  content: 'Normal';
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="1"]::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="1"]::before {
  content: 'Heading 1';
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="2"]::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="2"]::before {
  content: 'Heading 2';
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="3"]::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="3"]::before {
  content: 'Heading 3';
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="4"]::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="4"]::before {
  content: 'Heading 4';
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="5"]::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="5"]::before {
  content: 'Heading 5';
}

.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="6"]::before,
.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="6"]::before {
  content: 'Heading 6';
}
</style>