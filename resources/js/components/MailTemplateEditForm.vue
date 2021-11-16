<template>
  <div>
    <div v-if="data" class="flex -mx-2 -my-4">
      <div class="w-2/3 px-2 py-4">
        <h2 class="mt-4 mb-2">{{ __('General') }}</h2>
        <div class="card">
          <form-text-field :field="fields.name" :errors="validationErrors" />
          <form-text-field
            v-show="false"
            :field="fields.mail_class"
            :errors="validationErrors"
          />
          <default-field
            v-if="designs"
            :field="fields.design"
            :errors="validationErrors"
          >
            <template #field>
              <select
                v-model="selectedDesign"
                class="form-control form-select w-full"
              >
                <option v-for="option in designOptions" :value="option.value">
                  {{ option.label }}
                </option>
              </select>
            </template>
          </default-field>
        </div>
        <h2 class="mt-4 mb-2">{{ __('Recipients') }}</h2>
        <div class="card">
          <form-select-field-multiple
            v-if="variables.recipients"
            :field="fields.recipients"
            :errors="validationErrors"
          />
          <form-select-field-multiple
            :field="fields.cc"
            :tag-placeholder="__('Click to add email address')"
            :errors="validationErrors"
          />
          <form-select-field-multiple
            :field="fields.bcc"
            :tag-placeholder="__('Click to add email address')"
            :errors="validationErrors"
          />
        </div>
      </div>
    </div>
    <h2 class="mt-4 mb-2">{{ __('Localized fields') }}</h2>
    <div class="flex -mx-2 -my-4">
      <div v-if="data" class="w-2/3 px-2 py-4">
        <div class="card">
          <div
            v-for="locale in $config.locales"
            v-show="currentLocale === locale"
            :key="locale"
          >
            <form-text-field
              :field="fields[`sender_name___${locale}`]"
              :errors="validationErrors"
            />
            <form-text-field
              :field="fields[`sender_email___${locale}`]"
              :errors="validationErrors"
            />
            <form-text-field
              :field="fields[`subject___${locale}`]"
              :errors="validationErrors"
            />
            <div :data-field="`body___${locale}`">
              <component
                :is="$config.bodyFieldComponent"
                :field="fields[`body___${locale}`]"
                :errors="validationErrors"
              />
            </div>
            <form-file-field-multiple
              :field="fields[`attachments___${locale}`]"
              :errors="validationErrors"
            />
          </div>
        </div>
        <div class="flex items-center mt-4">
          <router-link
            :to="{ name: 'mail-index' }"
            class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6"
          >
            {{ __('Cancel') }}
          </router-link>

          <button
            @click="submit"
            class="btn btn-default btn-primary inline-flex items-center relative"
          >
            {{
              __(`${action === 'update' ? 'Update' : 'Create'} mail template`)
            }}
          </button>
        </div>
      </div>
      <div class="w-1/3 px-2 py-4 self-start sticky pin-t">
        <div class="card p-4 mb-4">
          <h2 class="mb-2">{{ __('Locale') }}</h2>
          <div class="flex -mx-1">
            <div v-for="locale in $config.locales" :key="locale" class="px-1">
              <button
                type="button"
                @click="currentLocale = locale"
                class="px-2 py-1 rounded"
                :class="{
                  'bg-30': currentLocale !== locale,
                  'bg-90 text-white': currentLocale === locale
                }"
              >
                {{ locale.toUpperCase() }}
              </button>
            </div>
          </div>
        </div>
        <div v-if="variables.content" class="card p-4">
          <h2 class="mb-2">{{ __('Variables') }}</h2>
          <p class="mb-2">
            {{
              __(
                'Click to inject at current caret position. The [[variables]] will be replaced with actual data before the mails are sent.'
              )
            }}
          </p>
          <div class="flex flex-wrap -mx-1">
            <div
              v-for="(variable, key) in variables.content"
              :key="key"
              class="px-1 pt-2"
            >
              <button
                @click="injectVariable(key)"
                type="button"
                class="px-2 py-1 rounded bg-30"
              >
                {{ variable }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Errors } from 'laravel-nova';
import { tap, each } from 'lodash-es';

import FormEditorjsField from './Form/EditorjsField.vue';
import FormFileFieldMultiple from './Form/FileFieldMultiple.vue';
import FormSelectFieldMultiple from './Form/SelectFieldMultiple.vue';

export default {
  components: {
    FormEditorjsField,
    FormFileFieldMultiple,
    FormSelectFieldMultiple
  },
  props: {
    data: {
      type: Object,
      default: () => null
    },
    action: {
      type: String,
      default: () => 'update'
    }
  },
  data: () => ({
    currentLocale: null,
    fields: {},
    variables: {},
    designs: {},
    selectedDesign: null,
    focusedField: null,
    focusedValue: null,
    caretPosition: null,
    validationErrors: new Errors()
  }),
  computed: {
    designOptions() {
      return Object.keys(this.designs).map(value => ({
        label: this.designs[value],
        value
      }));
    }
  },
  created() {
    this.currentLocale = this.$config.locales[0] || 'en';

    this.initializeFields();
    this.fetchVariables();
    this.fetchDesigns();
  },
  mounted() {
    this.initializeEvents();
  },
  methods: {
    initializeFields() {
      const { $config, data, __ } = this;

      this.fields = {
        name: { attribute: 'name', name: __('Name'), required: true },
        mail_class: {
          attribute: 'mail_class',
          name: __('Mail type'),
          readonly: true
        },
        design: {
          attribute: 'design',
          name: __('Design'),
          required: true,
          fill: formData => {
            formData.append('design', this.selectedDesign);
          }
        },
        sender_name: {
          attribute: 'sender_name',
          name: __('Sender name'),
          required: true
        },
        sender_email: {
          attribute: 'sender_email',
          name: __('Sender email'),
          required: true
        },
        recipients: {
          attribute: 'recipients',
          name: __('Recipients'),
          required: true
        },
        cc: {
          attribute: 'cc',
          name: __('CC'),
        },
        bcc: {
          attribute: 'bcc',
          name: __('BCC'),
        },
        subject: {
          attribute: 'subject',
          name: __('Subject'),
          required: true
        },
        body: { attribute: 'body', name: __('Body'), required: true },
        attachments: {
          attribute: 'attachments',
          name: __('Attachments')
        }
      };

      Object.keys(this.fields).forEach(attribute => {
        if ($config.localizedFields.includes(attribute)) {
          $config.locales.forEach(locale => {
            const localizedAttribute = `${attribute}___${locale}`;

            this.fields[localizedAttribute] = {
              ...this.fields[attribute],
              name: `${this.fields[attribute].name} (${locale.toUpperCase()})`,
              attribute: localizedAttribute,
              value: data[localizedAttribute] || null
            };
          });

          delete this.fields[attribute];
        } else {
          if (attribute === 'recipients' || attribute === 'cc' || attribute === 'bcc') {
            this.fields[attribute].value = [];

            if (data[attribute]) {
              this.fields[attribute].options = Object.keys(data[attribute]).map(key => {
                const value = data[attribute][key];
                const option = { label: value, value };
                this.fields[attribute].value.push(option);
                return option;
              });
              return;
            }
          }
          this.fields[attribute].value = data[attribute];
        }
      });

      // TODO implement when other editors are supported
      // if ($config.bodyFieldComponent !== 'form-editor-field') {
      //   $config.locales.forEach(locale => {
      //     const localizedAttribute = `body___${locale}`;
      //     this.fields[localizedAttribute].value = data[localizedAttribute]
      //       ? data[localizedAttribute].blocks[0].data
      //       : '';
      //   });
      // }
    },
    initializeEvents() {
      let focusTimeout = null;

      const handleFocus = event => {
        this.focusedField = event.target.id;
        this.focusedValue = event.target.value;

        if (focusTimeout) {
          clearTimeout(focusTimeout);
        }

        focusTimeout = setTimeout(() => {
          const selectionStart = event.target.selectionStart || 0;
          this.caretPosition = [
            selectionStart,
            event.target.selectionEnd || selectionStart
          ];
        }, 100);
      };

      [].forEach.call(this.$el.querySelectorAll('input'), input => {
        input.addEventListener('focus', handleFocus);
        input.addEventListener('click', handleFocus);
        input.addEventListener('keyup', handleFocus);
      });

      addEventListener("trix-initialize", event => {
        const { toolbarElement } = event.target
        const inputElement = toolbarElement.querySelector("input[name=href]")
        inputElement.type = "text"
      })

      const handleEditorFocus = event => {
        const element = event.target.closest('trix-editor');
        const parent = element.closest('[data-field]');
        const editor = element.editor;

        this.focusedField = parent ? parent.dataset.field : null;
        this.focusedValue = null;

        if (focusTimeout) {
          clearTimeout(focusTimeout);
        }

        focusTimeout = setTimeout(() => {
          this.caretPosition = editor.getSelectedRange();
        }, 100);
      };

      [].forEach.call(this.$el.querySelectorAll('trix-editor'), element => {
        element.id = element.closest('[data-field]').dataset.field;

        element.addEventListener('trix-change', handleEditorFocus);
        element.addEventListener('trix-focus', handleEditorFocus);
        element.addEventListener('click', handleEditorFocus);
      });
    },
    injectVariable(key) {
      const { focusedField, focusedValue, caretPosition } = this;

      const element = document.getElementById(focusedField);

      const injectedString = `[[${key}]]`;

      //  Trix editor
      if (focusedValue === null) {
        const editor = element.editor;
        editor.setSelectedRange(caretPosition);
        editor.insertString(injectedString);
      } else {
        const value = [
          focusedValue.slice(0, caretPosition[0]),
          injectedString,
          focusedValue.slice(caretPosition[1])
        ].join('');

        this.fields[focusedField].value = value;
        element.value = value;
      }
    },
    async fetchVariables() {
      const { data } = this;
      const mailClass = this.$route.params.templateId || data.mail_class;

      this.variables = await this.getApiData(
        `/templates/${mailClass}/variables`
      );

      //Recipients
      const recipients = this.variables.recipients || {};

      Object.keys(this.fields.recipients.value).map(key => {
        const value = this.fields.recipients.value[key];
        if(recipients[value.label]){
          this.fields.recipients.value[key].label = recipients[value.label];
        }
      });

      this.fields.recipients.options = Object.keys(recipients).map(value => {
        const option = { label: recipients[value], value };
        return option;
      });
    },
    async fetchDesigns() {
      const { data } = this;

      this.designs = await this.getApiData(`/designs`);

      Object.keys(this.designs).forEach(value => {
        if (data.design === value) {
          this.selectedDesign = value;
        }
      });
    },
    submit() {
      const submitUrl = {
        create: '/mail-templates/store',
        update: `/mail-templates/${this.data.id}/update`
      }[this.action];

      const formData = tap(new FormData(), formData => {
        each(this.fields, field => {
          field.fill(formData);
        });
      });

      formData.append(
        'mail_class',
        this.data.mail_class || this.$route.params.templateId
      );

      this.validationErrors = new Errors();

      this.postFormData(submitUrl, formData)
        .then(() => {
          this.$router.push({ name: 'mail-index' });
        })
        .catch(({ response }) => {
          this.validationErrors = new Errors(response.data.errors);
        });
    }
  }
};
</script>
