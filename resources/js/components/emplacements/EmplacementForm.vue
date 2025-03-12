<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h2>{{ isEditing ? 'Modifier l\'emplacement' : 'Nouvel emplacement' }}</h2>
      </div>

      <div class="card-body">
        <div v-if="message" class="alert" :class="messageClass" role="alert">
          {{ message }}
        </div>

        <form @submit.prevent="submitForm" enctype="multipart/form-data">
          <div class="form-group mb-3">
            <label for="nom">Nom de l'emplacement</label>
            <input type="text"
                   class="form-control"
                   :class="{ 'is-invalid': errors.nom }"
                   id="nom"
                   v-model="form.nom"
                   required>
            <div v-if="errors.nom" class="invalid-feedback">
              {{ errors.nom[0] }}
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="etablissement_id">Établissement</label>
            <select class="form-control"
                    :class="{ 'is-invalid': errors.etablissement_id }"
                    id="etablissement_id"
                    v-model="form.etablissement_id"
                    required>
              <option value="">Sélectionnez un établissement</option>
              <option v-for="etablissement in etablissements"
                      :key="etablissement.id"
                      :value="etablissement.id">
                {{ etablissement.nom }}
              </option>
            </select>
            <div v-if="errors.etablissement_id" class="invalid-feedback">
              {{ errors.etablissement_id[0] }}
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea class="form-control"
                      :class="{ 'is-invalid': errors.description }"
                      id="description"
                      v-model="form.description"
                      rows="3">
            </textarea>
            <div v-if="errors.description" class="invalid-feedback">
              {{ errors.description[0] }}
            </div>
          </div>

          <div v-if="isEditing && emplacement.photo_path" class="form-group mb-3">
            <label>Photo actuelle</label>
            <div>
              <img :src="'/storage/' + emplacement.photo_path"
                   :alt="'Photo de ' + emplacement.nom"
                   class="img-thumbnail"
                   style="max-width: 200px">
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="photo">{{ isEditing ? 'Modifier la photo' : 'Photo' }}</label>
            <input type="file"
                   class="form-control"
                   :class="{ 'is-invalid': errors.photo }"
                   id="photo"
                   @change="handleFileUpload"
                   accept="image/*">
            <small class="form-text text-muted">
              Format accepté : jpg, png, gif. Taille maximale : 2Mo
            </small>
            <div v-if="errors.photo" class="invalid-feedback">
              {{ errors.photo[0] }}
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'emplacements.index' }" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> Retour
            </router-link>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <i class="fas fa-save"></i>
              {{ isEditing ? 'Enregistrer les modifications' : 'Créer l\'emplacement' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import axios from 'axios'
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'EmplacementForm',

  props: {
    id: {
      type: String,
      required: false
    }
  },

  data() {
    return {
      form: {
        nom: '',
        description: '',
        etablissement_id: '',
        photo: null as File | null
      },
      emplacement: null as any,
      etablissements: [] as any[],
      errors: {} as Record<string, string[]>,
      message: '',
      messageClass: '',
      loading: false
    }
  },

  computed: {
    isEditing(): boolean {
      return !!this.id
    }
  },

  async created() {
    await this.fetchEtablissements()
    if (this.isEditing) {
      await this.fetchEmplacement()
    }
  },

  methods: {
    async fetchEtablissements() {
      try {
        const response = await axios.get('/api/etablissements')
        this.etablissements = response.data
      } catch (error) {
        this.showError('Erreur lors du chargement des établissements')
        console.error(error)
      }
    },

    async fetchEmplacement() {
      try {
        const response = await axios.get(`/api/emplacements/${this.id}`)
        this.emplacement = response.data
        this.form = {
          ...this.form,
          nom: this.emplacement.nom,
          description: this.emplacement.description,
          etablissement_id: this.emplacement.etablissement_id
        }
      } catch (error) {
        this.showError('Erreur lors du chargement de l\'emplacement')
        console.error(error)
      }
    },

    handleFileUpload(event: Event) {
      const target = event.target as HTMLInputElement
      if (target.files && target.files.length > 0) {
        this.form.photo = target.files[0]
      }
    },

    async submitForm() {
      this.loading = true
      this.errors = {}

      const formData = new FormData()
      formData.append('nom', this.form.nom)
      formData.append('description', this.form.description || '')
      formData.append('etablissement_id', this.form.etablissement_id)
      if (this.form.photo) {
        formData.append('photo', this.form.photo)
      }

      try {
        if (this.isEditing) {
          await axios.post(`/api/emplacements/${this.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          this.showSuccess('Emplacement modifié avec succès')
        } else {
          await axios.post('/api/emplacements', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          this.showSuccess('Emplacement créé avec succès')
        }
        this.$router.push({ name: 'emplacements.index' })
      } catch (error: any) {
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          this.showError('Une erreur est survenue')
        }
        console.error(error)
      } finally {
        this.loading = false
      }
    },

    showSuccess(message: string) {
      this.message = message
      this.messageClass = 'alert-success'
      setTimeout(() => this.message = '', 3000)
    },

    showError(message: string) {
      this.message = message
      this.messageClass = 'alert-danger'
      setTimeout(() => this.message = '', 3000)
    }
  }
})
</script>
