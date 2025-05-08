<template>
  <div class="file-uploader">
    <div class="file-input-container">
      <input
        type="file"
        ref="fileInput"
        class="file-input"
        @change="handleFileChange"
        :accept="acceptedFormats"
        :disabled="loading"
        :multiple="multiple"
      />
      <button
        type="button"
        class="file-select-button"
        @click="triggerFileInput"
        :disabled="loading"
      >
        {{ buttonText }}
      </button>
      <span class="file-name" v-if="selectedFile">{{ selectedFile.name }}</span>
    </div>
    
    <div v-if="error" class="file-error">
      {{ error }}
    </div>
    
    <div v-if="progressVisible" class="upload-progress">
      <div class="progress-track">
        <div
          class="progress-bar"
          :style="{ width: `${uploadProgress}%` }"
        ></div>
      </div>
      <span class="progress-text">{{ uploadProgress }}%</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FileUploader',
  props: {
    buttonText: {
      type: String,
      default: 'Select File'
    },
    acceptedFormats: {
      type: String,
      default: '.doc,.docx,.pdf'
    },
    multiple: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      selectedFile: null,
      loading: false,
      error: null,
      uploadProgress: 0,
      progressVisible: false
    }
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    handleFileChange(event) {
      const files = event.target.files
      
      if (!files || files.length === 0) {
        this.selectedFile = null
        return
      }
      
      const file = files[0]
      
      // Validate file type
      const fileExtension = file.name.split('.').pop().toLowerCase()
      const acceptedExtensions = this.acceptedFormats
        .split(',')
        .map(ext => ext.trim().replace('.', '').toLowerCase())
      
      if (!acceptedExtensions.includes(fileExtension)) {
        this.error = `Invalid file type. Accepted formats: ${this.acceptedFormats}`
        this.selectedFile = null
        return
      }
      
      // Clear previous errors and set the new file
      this.error = null
      this.selectedFile = file
      this.$emit('file-selected', file)
    },
    async uploadFile(url, additionalData = {}) {
      if (!this.selectedFile) {
        this.error = 'Please select a file first'
        return null
      }
      
      this.loading = true
      this.error = null
      this.progressVisible = true
      this.uploadProgress = 0
      
      try {
        const formData = new FormData()
        formData.append('file', this.selectedFile)
        
        // Add any additional data
        Object.entries(additionalData).forEach(([key, value]) => {
          formData.append(key, value)
        })
        
        const xhr = new XMLHttpRequest()
        
        // Track upload progress
        xhr.upload.addEventListener('progress', (event) => {
          if (event.lengthComputable) {
            this.uploadProgress = Math.round((event.loaded / event.total) * 100)
          }
        })
        
        // Create a promise to handle the XHR request
        const uploadPromise = new Promise((resolve, reject) => {
          xhr.open('POST', url)
          
          xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
              resolve(JSON.parse(xhr.responseText))
            } else {
              reject(new Error(xhr.statusText || 'Upload failed'))
            }
          }
          
          xhr.onerror = () => reject(new Error('Network error'))
          xhr.send(formData)
        })
        
        const response = await uploadPromise
        
        // Reset the file input
        this.$refs.fileInput.value = ''
        this.selectedFile = null
        
        this.$emit('upload-success', response)
        return response
      } catch (error) {
        this.error = error.message || 'File upload failed'
        this.$emit('upload-error', error)
        return null
      } finally {
        this.loading = false
        
        // Hide progress after a short delay to show completion
        setTimeout(() => {
          this.progressVisible = false
        }, 1000)
      }
    },
    reset() {
      this.selectedFile = null
      this.error = null
      this.uploadProgress = 0
      this.progressVisible = false
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = ''
      }
    }
  }
}
</script>

<style scoped>
.file-uploader {
  margin-bottom: var(--spacing-lg);
}

.file-input-container {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: var(--spacing-md);
}

.file-input {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

.file-select-button {
  display: inline-block;
  padding: var(--spacing-sm) var(--spacing-lg);
  background-color: var(--color-primary);
  color: white;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  border: none;
  transition: background-color var(--transition-fast);
}

.file-select-button:hover {
  background-color: var(--color-primary-light);
}

.file-select-button:disabled {
  background-color: var(--color-text-secondary);
  cursor: not-allowed;
}

.file-name {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  padding: var(--spacing-sm) 0;
  color: var(--color-text);
}

.file-error {
  margin-top: var(--spacing-sm);
  color: var(--color-error);
  font-size: 0.9rem;
}

.upload-progress {
  margin-top: var(--spacing-md);
  display: flex;
  align-items: center;
  gap: var(--spacing-md);
}

.progress-track {
  flex: 1;
  height: 8px;
  background-color: var(--color-border);
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: var(--color-primary);
  transition: width 0.3s ease;
}

.progress-text {
  font-size: 0.9rem;
  color: var(--color-text-secondary);
  min-width: 40px;
}
</style>