<template>
  <div>
    <div v-if="data" class="flex -mx-2 -my-4">
      <div class="w-2/3 px-2 py-4">
        <h2 class="mt-4 mb-2">{{ __('General') }}</h2>
        <div class="card">
          <form-text-field :field="fields.name" />
          <form-text-field v-show="false" :field="fields.mail_class" />
          <form-text-field v-show="false" :field="fields.design" />
        </div>
        <h2 class="mt-4 mb-2">{{ __('Recipients') }}</h2>
        <div class="card">
          <form-select-field-multiple
            v-if="variables.recipients"
            :field="fields.recipients"
          />
          <form-text-field :field="fields.cc" />
          <form-text-field :field="fields.bcc" />
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
            <form-text-field :field="fields[`sender_name___${locale}`]" />
            <form-text-field :field="fields[`sender_email___${locale}`]" />
            <form-text-field :field="fields[`subject___${locale}`]" />
            <div :data-field="`body___${locale}`">
              <component
                :is="$config.bodyFieldComponent"
                :field="fields[`body___${locale}`]"
              />
            </div>
            <form-file-field-multiple
              :field="fields[`attachments___${locale}`]"
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
            {{ __('Create mail template') }}
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
    focusedField: null,
    focusedValue: null,
    caretPosition: null
  }),
  created() {
    this.currentLocale = this.$config.locales[0] || 'en';

    this.initializeFields();
    this.fetchVariables();
  },
  mounted() {
    this.initializeEvents();
  },
  methods: {
    initializeFields() {
      this.localizedFields = [
        'sender_name',
        'sender_email',
        'subject',
        'body',
        'attachments'
      ];

      const { $config, data, localizedFields, __ } = this;

      this.fields = {
        name: { attribute: 'name', name: __('Name'), required: true },
        mail_class: {
          attribute: 'mail_class',
          name: __('Mail type'),
          readonly: true
        },
        design: { attribute: 'design', name: __('Design'), readonly: true },
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
          name: __('CC')
        },
        bcc: {
          attribute: 'bcc',
          name: __('BCC')
        },
        subject: {
          attribute: 'subject',
          name: __('Subject'),
          required: true
        },
        body: { attribute: 'body', name: __('Body'), required: true },
        /*attachments: {
          attribute: 'attachments',
          name: __('Attachments')
        }*/
      };

      Object.keys(this.fields).forEach(attribute => {
        if (localizedFields.includes(attribute)) {
          $config.locales.forEach(locale => {
            const localizedAttribute = `${attribute}___${locale}`;

            this.fields[localizedAttribute] = {
              ...this.fields[attribute],
              name: `${this.fields[attribute].name} (${locale.toUpperCase()})`,
              attribute: localizedAttribute,
              value: data[attribute][locale] || null
            };
          });

          delete this.fields[attribute];
        } else {
          this.fields[attribute].value = data[attribute];
        }
      });

      if ($config.bodyFieldComponent !== 'form-editor-field') {
        $config.locales.forEach(locale => {
          this.fields[`body___${locale}`].value = data.body[locale]
            ? data.body[locale].blocks[0].data
            : '';
        });
      }
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

      const handleEditorFocus = event => {
        const element = event.target.closest('trix-editor');
        const parent = element.closest('[data-field]');
        const editor = element.editor;
        const selection = document.getSelection();

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
      const { fields, focusedField, focusedValue, caretPosition } = this;

      const element = document.getElementById(focusedField);

      const injectedString = `[[ ${key} ]]`;

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
      this.variables = await this.getApiData(
        `/templates/${this.$route.params.templateId}/variables`
      );

      const recipients = this.variables.recipients;

      this.fields.recipients.value = [];

      this.fields.recipients.options = Object.keys(recipients).map(value => {
        const option = { label: recipients[value], value };
        if (data.recipients.includes(value)) {
          this.fields.recipients.value.push(option);
        }
        return option;
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

      formData.append('mail_class', this.$route.params.templateId);

      this.errors = null;

      this.postFormData(submitUrl, formData)
        .then(() => {
          this.$router.push({ name: 'mail-index' });
        })
        .catch(error => {
          this.errors = error.data.errors;
        });
    }
  }
};
</script>
