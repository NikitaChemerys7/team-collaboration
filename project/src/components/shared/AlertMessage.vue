<template>
  <transition name="alert">
    <div
      v-if="show"
      class="alert-message"
      :class="[`alert-${type}`, dismissible ? 'alert-dismissible' : '']"
    >
      <div class="alert-icon" v-if="showIcon">
        <span v-if="type === 'success'">✓</span>
        <span v-else-if="type === 'error'">✕</span>
        <span v-else-if="type === 'warning'">!</span>
        <span v-else-if="type === 'info'">i</span>
      </div>
      <div class="alert-content">
        <h4 v-if="title" class="alert-title">{{ title }}</h4>
        <p class="alert-text">{{ message }}</p>
      </div>
      <button
        v-if="dismissible"
        class="alert-close"
        @click="closeAlert"
        aria-label="Close alert"
      >
        ×
      </button>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'AlertMessage',
  props: {
    type: {
      type: String,
      default: 'info',
      validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
      type: String,
      default: ''
    },
    message: {
      type: String,
      required: true
    },
    dismissible: {
      type: Boolean,
      default: true
    },
    duration: {
      type: Number,
      default: 0 // 0 means it won't auto-dismiss
    },
    showIcon: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      show: true,
      timer: null
    }
  },
  mounted() {
    if (this.duration > 0) {
      this.timer = setTimeout(() => {
        this.closeAlert()
      }, this.duration)
    }
  },
  beforeUnmount() {
    if (this.timer) {
      clearTimeout(this.timer)
    }
  },
  methods: {
    closeAlert() {
      this.show = false
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
.alert-message {
  display: flex;
  align-items: center;
  padding: var(--spacing-md) var(--spacing-lg);
  border-radius: var(--border-radius-md);
  margin-bottom: var(--spacing-lg);
  position: relative;
  animation: alertIn 0.3s ease forwards;
}

@keyframes alertIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-success {
  background-color: rgba(76, 175, 80, 0.1);
  border-left: 4px solid var(--color-success);
  color: #2e7d32;
}

.alert-error {
  background-color: rgba(244, 67, 54, 0.1);
  border-left: 4px solid var(--color-error);
  color: #d32f2f;
}

.alert-warning {
  background-color: rgba(255, 152, 0, 0.1);
  border-left: 4px solid var(--color-warning);
  color: #ef6c00;
}

.alert-info {
  background-color: rgba(13, 71, 161, 0.1);
  border-left: 4px solid var(--color-primary);
  color: var(--color-primary-dark);
}

.alert-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  margin-right: var(--spacing-md);
  flex-shrink: 0;
}

.alert-success .alert-icon {
  background-color: var(--color-success);
  color: white;
}

.alert-error .alert-icon {
  background-color: var(--color-error);
  color: white;
}

.alert-warning .alert-icon {
  background-color: var(--color-warning);
  color: white;
}

.alert-info .alert-icon {
  background-color: var(--color-primary);
  color: white;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-weight: 600;
  margin-bottom: var(--spacing-xs);
  font-size: 1rem;
}

.alert-text {
  margin: 0;
  font-size: 0.9rem;
}

.alert-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0;
  line-height: 1;
  margin-left: var(--spacing-md);
  opacity: 0.7;
  transition: opacity var(--transition-fast);
}

.alert-close:hover {
  opacity: 1;
}

.alert-enter-active,
.alert-leave-active {
  transition: all 0.3s ease;
}

.alert-enter-from,
.alert-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>