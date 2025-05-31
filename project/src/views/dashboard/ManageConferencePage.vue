<template>
  <div class="manage-conference flex h-full">
    <!-- Sidebar -->
    <aside class="sidebar bg-gray-50 p-8 w-72 flex-shrink-0 border-r">
      <h2 class="text-2xl font-bold mb-8">Conferences</h2>
      <div class="mb-8">
        <div class="flex gap-4 items-center flex-wrap mb-4">
          <button
            v-for="year in years"
            :key="year"
            :class="[
              'year-button',
              year === selectedYear ? 'year-button-active' : 'year-button-inactive'
            ]"
            @click="selectedYear = year"
          >
            {{ year }}
          </button>
        </div>
        <div class="flex items-center gap-2">
          <input
            v-model="newYear"
            type="number"
            class="form-control w-24"
            placeholder="Year"
            min="2000"
            max="2100"
          />
          <button
            @click="addNewYear"
            class="add-year-button"
            :disabled="!newYear || years.includes(Number(newYear))"
          >
            Add
          </button>
        </div>
      </div>
      <button class="btn btn-primary mb-6">Create New</button>
      <div class="conference-list bg-gray-100 rounded-lg p-4">
        <div
          v-for="conf in filteredConferences"
          :key="conf.id"
          class="py-3 px-4 rounded-lg hover:bg-blue-100 cursor-pointer mb-2 transition-colors duration-200"
          :class="{ 'bg-blue-200 font-bold': conf.id === editingId }"
          @click="editConference(conf.id)"
        >
          {{ conf.title }}
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
      <div v-if="loading" class="flex justify-center items-center h-full">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
      </div>
      <div v-else class="card">
        <form @submit.prevent="saveConference" class="conference-form">
          <div class="form-group">
            <label for="title" class="form-label">Conference Title</label>
            <input
              id="title"
              v-model="form.title"
              class="form-control"
              required
              :disabled="saving"
            />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="year" class="form-label">Year</label>
              <input
                id="year"
                v-model.number="form.year"
                type="number"
                class="form-control"
                required
                :disabled="saving"
              />
            </div>
            <div class="form-group">
              <label for="date" class="form-label">Date</label>
              <input
                id="date"
                v-model="form.date"
                type="date"
                class="form-control"
                required
                :disabled="saving"
              />
            </div>
            <div class="form-group">
              <label for="location" class="form-label">Location</label>
              <input
                id="location"
                v-model="form.location"
                class="form-control"
                required
                :disabled="saving"
              />
            </div>
          </div>

          <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <Editor
              v-model="form.description"
              :init="editorConfig"
              :disabled="saving"
              class="tinymce-editor"
              api-key="bhzhqrj5zy8hc6r6y0dlz9fu1kz0guzcw13szbgryoumiw4t"
              :output-format="'html'"
            />
          </div>

          <div class="form-group">
            <label for="heroImage" class="form-label">Hero Image (URL)</label>
            <input
              id="heroImage"
              v-model="form.heroImage"
              class="form-control"
              placeholder="https://..."
              :disabled="saving"
            />
          </div>

          <div class="form-group">
            <label for="gallery" class="form-label">Gallery (comma-separated URLs)</label>
            <input
              id="gallery"
              v-model="galleryInput"
              class="form-control"
              placeholder="https://img1.jpg, https://img2.jpg"
              :disabled="saving"
            />
            <div class="gallery-preview">
              <img v-for="img in form.gallery" :key="img" :src="img" class="gallery-thumbnail" />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Documents</label>
            <div v-for="(file, idx) in form.files" :key="idx" class="file-input-group">
              <input
                v-model="file.name"
                class="form-control"
                placeholder="File name"
                :disabled="saving"
              />
              <input
                v-model="file.url"
                class="form-control"
                placeholder="URL"
                :disabled="saving"
              />
              <button
                type="button"
                @click="removeFile(idx)"
                class="btn btn-danger"
                :disabled="saving"
              >
                Remove
              </button>
            </div>
            <button
              type="button"
              @click="addFile"
              class="btn btn-secondary mt-2"
              :disabled="saving"
            >
              Add File
            </button>
          </div>

          <div class="form-group">
            <label class="form-label">Speakers</label>
            <div v-for="(speaker, idx) in form.speakers" :key="idx" class="speaker-input-group">
              <input
                v-model="speaker.name"
                class="form-control"
                placeholder="Name"
                :disabled="saving"
              />
              <input
                v-model="speaker.role"
                class="form-control"
                placeholder="Role"
                :disabled="saving"
              />
              <input
                v-model="speaker.workplace"
                class="form-control"
                placeholder="Workplace"
                :disabled="saving"
              />
              <input
                v-model="speaker.photo"
                class="form-control"
                placeholder="Photo URL"
                :disabled="saving"
              />
              <button
                type="button"
                @click="removeSpeaker(idx)"
                class="btn btn-danger"
                :disabled="saving"
              >
                Remove
              </button>
            </div>
            <button
              type="button"
              @click="addSpeaker"
              class="btn btn-secondary mt-2"
              :disabled="saving"
            >
              Add Speaker
            </button>
          </div>

          <div class="form-actions">
            <button
              v-if="editingId"
              type="button"
              @click="deleteConference"
              class="btn btn-danger"
              :disabled="saving"
            >
              Delete
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="saving"
            >
              {{ saving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useConferenceStore } from '../../stores/conference'
import Editor from '@tinymce/tinymce-vue'

const route = useRoute()
const router = useRouter()
const store = useConferenceStore()

const loading = ref(false)
const saving = ref(false)
const editingId = ref(null)
const selectedYear = ref(new Date().getFullYear())
const galleryInput = ref('')
const newYear = ref('')
const years = ref([new Date().getFullYear()])

const editorConfig = {
  height: 400,
  menubar: true,
  plugins: [
    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
    'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
  ],
  toolbar: 'undo redo | blocks | ' +
    'bold italic forecolor | alignleft aligncenter ' +
    'alignright alignjustify | bullist numlist outdent indent | ' +
    'removeformat | help',
  content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }'
}

const form = ref({
  title: '',
  year: selectedYear.value,
  date: '',
  location: '',
  description: '',
  heroImage: '',
  gallery: [],
  speakers: [],
  files: []
})

watch(() => store.conferences, (conferences) => {
  const allYears = conferences.map(c => c.year)
  const unique = Array.from(new Set([...allYears, new Date().getFullYear()]))
  years.value = unique.sort((a, b) => b - a)
}, { immediate: true })

const filteredConferences = computed(() =>
  store.conferences.filter(c => c.year === selectedYear.value)
)

function resetForm() {
  form.value = {
    title: '',
    year: selectedYear.value,
    date: '',
    location: '',
    description: '',
    heroImage: '',
    gallery: [],
    speakers: [],
    files: []
  }
  galleryInput.value = ''
  editingId.value = null
}

function startCreate() {
  resetForm()
  router.replace({ name: 'manage-conference' })
}

async function editConference(id) {
  loading.value = true
  await store.fetchConferenceById(id)
  const conf = store.currentConference
  if (conf) {
    form.value = JSON.parse(JSON.stringify(conf))
    if (form.value.date) {
      form.value.date = new Date(form.value.date).toISOString().split('T')[0]
    }
    galleryInput.value = conf.gallery ? conf.gallery.join(', ') : ''
    editingId.value = conf.id
    selectedYear.value = conf.year
    router.replace({ name: 'manage-conference', params: { id: conf.id } })
  }
  loading.value = false
}

function addSpeaker() {
  form.value.speakers.push({ name: '', role: '', workplace: '', photo: '' })
}

function removeSpeaker(idx) {
  form.value.speakers.splice(idx, 1)
}

function addFile() {
  form.value.files.push({ name: '', url: '' })
}

function removeFile(idx) {
  form.value.files.splice(idx, 1)
}

watch(galleryInput, (val) => {
  form.value.gallery = val.split(',').map(s => s.trim()).filter(Boolean)
})

async function saveConference() {
  saving.value = true
  try {
    const dataToSave = {
      ...form.value,
      date: form.value.date ? new Date(form.value.date).toISOString().split('T')[0] : null
    }
    
    if (editingId.value) {
      await store.updateConference(editingId.value, dataToSave)
    } else {
      await store.createConference(dataToSave)
    }
    await store.fetchConferences()
    saving.value = false
    resetForm()
    router.replace({ name: 'manage-conference' })
  } catch (error) {
    console.error('Error saving conference:', error)
    saving.value = false
  }
}

async function deleteConference() {
  if (!editingId.value) return
  if (!confirm('Are you sure you want to delete this conference?')) return
  await store.deleteConference(editingId.value)
  await store.fetchConferences()
  resetForm()
  router.replace({ name: 'manage-conference' })
}

function addNewYear() {
  const year = Number(newYear.value)
  if (!year || years.value.includes(year)) return
  
  years.value = [...years.value, year].sort((a, b) => b - a)
  selectedYear.value = year
  newYear.value = ''
}

onMounted(async () => {
  loading.value = true
  await store.fetchConferences()
  loading.value = false
  if (route.params.id) {
    editConference(Number(route.params.id))
  }
})
</script>

<style scoped>
.mb-8 {
  margin-bottom: var(--spacing-xl);
}

.manage-conference {
  min-height: 100vh;
  background-color: var(--color-background);
  font-family: 'Inter', Arial, sans-serif;
}

.sidebar {
  min-width: 260px;
  max-width: 320px;
  background-color: var(--color-surface);
  border-right: 1px solid var(--color-border);
  box-shadow: var(--shadow-md);
  padding: 20px;
}

.sidebar h2 {
  letter-spacing: -1px;
  color: var(--color-primary);
}

.conference-list {
  max-height: 60vh;
  overflow-y: auto;
  background-color: var(--color-background);
  border: 1px solid var(--color-border);
}

.conference-list > div {
  transition: all var(--transition-fast);
  border: 1px solid transparent;
  cursor: pointer;
}

.conference-list > div:hover {
  background-color: var(--color-primary-light);
  border-color: var(--color-primary);
}

.conference-list > div.bg-blue-200 {
  background-color: var(--color-primary-light);
  color: white;
  border-color: var(--color-primary);
}

.card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--spacing-xl);
}

.conference-form {
  max-width: 900px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: var(--spacing-lg);
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-lg);
}

.form-label {
  display: block;
  color: var(--color-text);
  font-weight: 500;
  margin-bottom: var(--spacing-xs);
}

.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid var(--color-border);
  border-radius: 0.5rem;
  background-color: var(--color-surface);
  color: var(--color-text);
  font-size: 1rem;
  transition: all 0.2s ease;
}

.form-control:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-primary-light);
}

.form-control:disabled {
  background-color: var(--color-background);
  cursor: not-allowed;
  opacity: 0.7;
}

.gallery-preview {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-sm);
  margin-top: var(--spacing-sm);
}

.gallery-thumbnail {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: var(--border-radius-sm);
}

.file-input-group,
.speaker-input-group {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: var(--spacing-sm);
  margin-bottom: var(--spacing-sm);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: var(--spacing-md);
  margin-top: var(--spacing-xl);
}

.btn {
  padding: var(--spacing-sm) var(--spacing-lg);
  border-radius: var(--border-radius-md);
  font-weight: 500;
  transition: all var(--transition-fast);
  cursor: pointer;
  border: none;
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-primary {
  background-color: var(--color-primary);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--color-primary-dark);
}

.btn-secondary {
  background-color: var(--color-secondary);
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background-color: var(--color-secondary-dark);
}

.btn-danger {
  background-color: var(--color-error);
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background-color: darkred;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

.year-button {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  transition: all 0.2s ease;
  border: 2px solid transparent;
  min-width: 100px;
  text-align: center;
}

.year-button-active {
  background-color: var(--color-primary);
  color: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

.year-button-inactive {
  background-color: var(--color-surface);
  color: var(--color-text);
  border-color: var(--color-border);
}

.year-button-inactive:hover {
  background-color: var(--color-primary-light);
  border-color: var(--color-primary);
  color: var(--color-primary);
  transform: translateY(-1px);
}

.add-year-button {
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  background-color: var(--color-secondary);
  color: white;
  border: none;
  transition: all 0.2s ease;
  min-width: 80px;
}

.add-year-button:hover:not(:disabled) {
  background-color: var(--color-secondary-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.add-year-button:disabled {
  background-color: var(--color-background);
  color: var(--color-text-secondary);
  cursor: not-allowed;
  opacity: 0.7;
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