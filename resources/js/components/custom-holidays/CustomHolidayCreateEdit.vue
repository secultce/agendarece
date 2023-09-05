<template>
    <div class="d-inline">
      <v-dialog
        v-model="dialog"
        width="700"
        :persistent="overlay"
        eager
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-if="!customHoliday" v-bind="attrs" v-on="on" color="primary" class="elevation-0" >
            <v-icon class="mr-1" small>fas fa-calendar</v-icon>
            Nova Data Comemorativa
          </v-btn>
          <v-btn
            v-else
            v-bind="attrs"
            v-on="on"
            class="elevation-0 mr-1"
            color="primary"
            title="Editar Data Comemorativa"
            fab 
            small
          >
            <v-icon small>fa-edit</v-icon>
          </v-btn>
        </template>
  
        <v-card class="px-3 pb-6 pt-2">
          <v-card-title
            primary-title
          >
            <v-spacer></v-spacer>
            <v-btn icon :disabled="overlay" @click="dialog = false">
              <v-icon>fas fa-times</v-icon>
            </v-btn>
          </v-card-title>
  
          <v-card-text>
            <div class="row">
              <div class="col-md-12">
                <label for="name">Nome <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fas fa-calendar"></i>
                    </span>
                  </div>
  
                  <input v-model="name" class="form-control" type="text" placeholder="Digite o nome">
                </div>
  
                <template v-for="(errorMessage, index) in errorMessages('name')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label class="mb-3">Data de Inicio <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="startAt"
                  v-mask="'##/##'"
                  type="tel"
                  label="Data de Inicio"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('start_at')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>

              <div class="col-md-6">
                <label class="mb-3">Data de Término <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="endAt"
                  v-mask="'##/##'"
                  type="tel"
                  label="Data de Término"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('end_at')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>

            <div class="row" v-if="dialog">
              <div class="col-md-12">
                <label for="description">Conteúdo (Opcional)</label>
                <editor
                  v-model="body"
                  :init="options"
                  api-key="e31s85ixm5tjctd1yx4wothremv0n9uvkcvstfyeuwoxi2dv"
                />
              </div>
            </div>

            <div class="row" v-if="isSectorSelectable">
              <div class="col-md-12">
                <label for="function">Equipamento Cultural (opcional)</label>
                <v-autocomplete
                  v-model="sector"
                  :items="sectorsList"
                  :loading="sectorsLoading"
                  item-text="name"
                  item-value="id"
                  label="Equipamento Cultural da Data comemorativa"
                  no-data-text="Nenhum equipamento cultural encontrado"
                  hide-details
                  clearable
                  solo
                ></v-autocomplete>
              </div>
            </div>
          </v-card-text>
  
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              class="elevation-0 mt-3 px-5"
              :loading="overlay"
              @click="saveCustomHoliday()"
            >
              Salvar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
  
      <v-overlay :value="overlay">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
    </div>
  </template>
  <script>
    import { maska } from 'maska';
    import Editor from '@tinymce/tinymce-vue';

    export default {
      directives: { mask: maska },
      components: { editor: Editor },
      data: () => ({
        overlay: false,
        dialog: false,
        sector: null,
        name: "",
        body: "",
        startAt: "",
        endAt: "",
        fieldErrors: [],
        sectorsLoading: true,
        sectorsList: [],
        options: {
          menubar: false,
          skin_url: '',
          forced_root_blocks: false,
          plugins: 'searchreplace directionality visualblocks visualchars image pagebreak nonbreaking insertdatetime advlist lists wordcount quickbars hr',
          toolbar: 'undo redo | fontsize blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify lineheight | outdent indent | numlist bullist | removeformat | insertfile image | ltr rtl hr',
          toolbar_sticky: true,
          height: 500,
          language: "pt_BR",
          content_style: `body { font-family: 'Oswald', sans-serif; font-size: 18px; } img[style*="float: left"] { margin: 5px 15px 0px 0px; } img[style*="float: right"] { margin: 5px 0px 0px 15px; }`,
          font_size_formats: "8px 9px 10px 11px 12px 13px 14px 16px 18px 20px 22px 24px 30px 32px 34px 36px 40px 44px 48px 60px 72px 96px",
          line_height_formats: "0.1 0.2 0.3 0.4 0.5 0.6 0.7 0.8 0.9 1 1.5 2 2.5 3",
          file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
              var file = this.files[0];

              var reader = new FileReader();

              reader.onload = function () {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);

                blobCache.add(blobInfo);

                cb(blobInfo.blobUri(), { title: file.name });
              };

              reader.readAsDataURL(file);
            };

            input.click();
          }
        }
      }),
      methods: {
        errorMessages(field) {
          if (!(`${field}` in this.fieldErrors)) return [];
  
          return this.fieldErrors[`${field}`];
        },
        saveCustomHoliday() {
          this.overlay = true;
          
          axios({
            method: this.customHoliday ? 'put' : 'post',
            url: `/api/custom-holiday${this.customHoliday ? `/${this.customHoliday.id}` : ''}`,
            data: {
              sector: this.isSectorSelectable ? this.sector : null,
              name: this.name,
              body: this.body,
              start_at: this.startAt.split('/').reverse().join('-'),
              end_at: this.endAt.split('/').reverse().join('-')
            }
          }).then(response => {
            this.$emit("success", response.data.message);
            this.clearCredentials();
  
            this.dialog = false;
          })
          .catch(error => {
            if ("errors" in error.response.data) {
              this.fieldErrors = error.response.data.errors;
  
              return;
            }
  
            this.$emit("error", error.response.data.message)
          })
          .finally(() => this.overlay = false);
        },
        listSectors() {
          this.sectorsLoading = true;
          this.sectorsList    = [];

          axios.get(`/api/sector`, {})
            .then(response => this.sectorsList = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.sectorsLoading = false)
          ;
        },
        clearCredentials() {
          this.sector  = null;
          this.name    = "";
          this.body    = "";
          this.startAt = "";
          this.endAt   = "";
        }
      },
      watch: {
        dialog() {
          if (!this.dialog) return;

          if (this.isSectorSelectable) this.listSectors();

          if (!this.customHoliday) return;
  
          this.sector  = this.customHoliday.sector_id;
          this.name    = this.customHoliday.name;
          this.startAt = this.customHoliday.start_at.split('-').reverse().join('/');
          this.endAt   = this.customHoliday.end_at.split('-').reverse().join('/');

          setTimeout(() => {
            this.body = this.customHoliday.body;
          }, 1000);
        }
      },
      computed: {
        isSectorSelectable() {
          return this.authUser.role.tag === 'administrator';
        }
      },
      props: {
        customHoliday: {},
        authUser: {}
      }
    }
  </script>