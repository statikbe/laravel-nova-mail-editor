<template>
  <default-field :field="{ stacked: false, ...field }" full-width-content>
    <template slot="field">
      <div class="w-full py-3 px-0 form-input-bordered">
        <editor :config="editorConfig" class="w-full" />
      </div>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

import HeaderTool from '@editorjs/header';
import RawTool from '@editorjs/raw';
import LinkTool from '@editorjs/link';
import SimpleImageTool from '@editorjs/simple-image';
import ImageTool from '@editorjs/image';
import ChecklistTool from '@editorjs/checklist';
import ListTool from '@editorjs/list';
import EmbedTool from '@editorjs/embed';
import QuoteTool from '@editorjs/quote';

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: {
    field: {
      type: Object,
      default: () => ({})
    },
    config: {
      type: Object,
      default: () => ({})
    }
  },
  data: () => ({
    editorConfig: {
      tools: {
        header: HeaderTool,
        raw: RawTool,
        link: LinkTool,
        simpleImage: SimpleImageTool,
        image: ImageTool,
        checklist: ChecklistTool,
        list: ListTool,
        embed: EmbedTool,
        quote: QuoteTool
      }
    }
  }),
  created() {
    this.editorConfig.data = JSON.parse(JSON.stringify(this.field.value));
  },
  methods: {
    fill(formData) {
      formData.append(this.field.attribute, JSON.stringify({}));
    }
  }
};
</script>
