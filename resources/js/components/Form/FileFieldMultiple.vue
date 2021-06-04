<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <span class="form-file mr-4" :class="{ 'opacity-75': isReadonly }">
        <input
          ref="fileField"
          :dusk="field.attribute"
          class="form-file-input select-none"
          type="file"
          :id="idAttr"
          name="name"
          multiple
          @change="fileChange"
          :disabled="isReadonly"
          :accept="field.acceptedTypes"
        />
        <label
          :for="labelFor"
          class="form-file-btn btn btn-default btn-primary select-none"
        >
          {{ __('Choose Files') }}
        </label>
      </span>

      <span class="text-gray-50 select-none" v-html="currentLabel"></span>

      <div v-if="files" class="mt-4">
        <div v-for="({ name }, index) in files" :key="index" class="mb-1">
          <span>{{ name }}</span>
          <button
            @click="removeFile(index)"
            class="font-bold text-danger ml-1 px-1"
          >
            &times;
          </button>
        </div>
      </div>

      <p v-if="hasError" class="text-xs mt-2 text-danger">
        {{ firstError }}
      </p>
    </template>
  </default-field>
</template>

<script>
// import ImageLoader from '@/components/ImageLoader';
// import DeleteButton from '@/components/DeleteButton';
import { FormField, HandlesValidationErrors, Errors } from 'laravel-nova';

export default {
  props: [
    'resourceId',
    'relatedResourceName',
    'relatedResourceId',
    'viaRelationship'
  ],

  mixins: [HandlesValidationErrors, FormField],

  //   components: { DeleteButton, ImageLoader },

  data: () => ({
    files: null,
    removeModalOpen: false,
    missing: false,
    deleted: false,
    uploadErrors: new Errors()
  }),

  mounted() {
    this.field.fill = formData => {
      if (this.files) {
        this.files.forEach(file => {
          formData.append(`${this.field.attribute}[]`, file);
        });
      }
    };
  },

  methods: {
    /**
     * Respond to the file change
     */
    fileChange(event) {
      this.files = Array.from(this.$refs.fileField.files);
    },

    /**
     * Confirm removal of the linked file
     */
    confirmRemoval() {
      this.removeModalOpen = true;
    },

    /**
     * Close the upload removal modal
     */
    closeRemoveModal() {
      this.removeModalOpen = false;
    },

    /**
     * Remove the linked file from storage
     */
    removeFile(index) {
      this.files.splice(index, 1);
    }
  },

  computed: {
    /**
     * Determine if the field has an upload error.
     */
    hasError() {
      return this.uploadErrors.has(this.fieldAttribute);
    },

    /**
     * Return the first error for the field.
     */
    firstError() {
      if (this.hasError) {
        return this.uploadErrors.first(this.fieldAttribute);
      }
    },

    /**
     * The current label of the file field.
     */
    currentLabel() {
      const { files, __ } = this;

      return files
        ? __(':files files selected', { files: files.length })
        : __('no files selected');
    },

    /**
     * The ID attribute to use for the file field.
     */
    idAttr() {
      return this.labelFor;
    },

    /**
     * The label attribute to use for the file field.
     */
    labelFor() {
      return `file-${this.field.attribute}`;
    },

    /**
     * Determine whether the field has a value.
     */
    hasValue() {
      return (
        Boolean(this.field.value || this.imageUrl) &&
        !Boolean(this.deleted) &&
        !Boolean(this.missing)
      );
    },

    /**
     * Determine whether the field should show the loader component.
     */
    shouldShowLoader() {
      return !Boolean(this.deleted) && Boolean(this.imageUrl);
    },

    /**
     * Determine whether the field should show the remove button.
     */
    shouldShowRemoveButton() {
      return Boolean(this.field.deletable);
    },

    /**
     * Return the preview or thumbnail URL for the field.
     */
    imageUrl() {
      return this.field.previewUrl || this.field.thumbnailUrl;
    },

    /**
     * Determine the maximum width of the field.
     */
    maxWidth() {
      return this.field.maxWidth || 320;
    }
  }
};
</script>
