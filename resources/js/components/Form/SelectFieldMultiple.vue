<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <multi-select
        v-model="value"
        :options="options"
        :placeholder="placeholder"
        @tag="addTag"
        track-by="value"
        label="label"
        select-label=""
        deselect-label=""
        hide-selected
        multiple
        taggable
      >
        <template #no-options>
          <span></span>
        </template>
      </multi-select>
    </template>
  </default-field>
</template>

<script>
import MultiSelect from 'vue-multiselect';

import { FormField, HandlesValidationErrors } from 'laravel-nova';

export default {
  mixins: [HandlesValidationErrors, FormField],

  components: { MultiSelect },

  data: () => ({
    value: [],
    options: []
  }),

  computed: {
    /**
     * Return the placeholder text for the field.
     */
    placeholder() {
      return this.field.placeholder || '';
    }
  },

  mounted() {
    this.value = this.field.value;
    this.options = this.field.options;

    this.field.fill = formData => {
      this.value.forEach(item => {
        formData.append(`${this.field.attribute}[]`, item.value);
      });
    };
  },

  methods: {
    addTag(value) {
      const option = { label: value, value };

      this.options.push(option);
      this.value.push(option);
    }
  }
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style lang="scss">
.multiselect,
.multiselect__tags {
  //   min-height: 2.25rem;
}

.multiselect__tags {
  background-color: var(--white);
  border-width: 1px;
  border-color: #bacad6;
  padding: 5px;
  color: #7c858e;
  border-radius: 0.5rem;
  line-height: normal;
  font-family: inherit;
  font-size: inherit;
}

.multiselect__option--highlight,
.multiselect__tag,
.multiselect__tag-icon {
  &,
  &:after {
    background: var(--primary);
    color: white;
  }
}

.multiselect__tag-icon {
  &:hover,
  &:focus {
    &,
    &:after {
      background: var(--primary-dark);
    }
  }
}
</style>
