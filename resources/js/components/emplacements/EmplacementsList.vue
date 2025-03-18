<template>
  <div class="container">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h2>Emplacements</h2>
        <router-link :to="{ name: 'emplacements.create' }" class="btn btn-primary">
          <i class="fas fa-plus"></i> Nouvel emplacement
        </router-link>
      </div>

      <div class="card-body">
        <div v-if="message" class="alert" :class="messageClass" role="alert">
          {{ message }}
        </div>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Établissement</th>
                <th>Description</th>
                <th>Photo</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="emplacement in emplacements" :key="emplacement.id">
                <td>{{ emplacement.nom }}</td>
                <td>{{ emplacement.etablissement.nom }}</td>
                <td>{{ truncate(emplacement.description, 50) }}</td>
                <td>
                  <img v-if="emplacement.photo_path"
                       :src="'/storage/' + emplacement.photo_path"
                       :alt="'Photo de ' + emplacement.nom"
                       class="img-thumbnail"
                       style="max-width: 50px">
                  <span v-else class="text-muted">Aucune photo</span>
                </td>
                <td>
                  <div class="btn-group">
                    <router-link :to="{ name: 'emplacements.show', params: { id: emplacement.id }}"
                                class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </router-link>
                    <router-link :to="{ name: 'emplacements.edit', params: { id: emplacement.id }}"
                                class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </router-link>
                    <button @click="deleteEmplacement(emplacement.id)"
                            class="btn btn-sm btn-danger">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'EmplacementsList',

  data() {
    return {
      emplacements: [],
      message: '',
      messageClass: ''
    }
  },

  created() {
    this.fetchEmplacements()
  },

  methods: {
    async fetchEmplacements() {
      try {
        const response = await axios.get('/api/emplacements')
        this.emplacements = response.data
      } catch (error) {
        this.showError('Erreur lors du chargement des emplacements')
        console.error(error)
      }
    },

    async deleteEmplacement(id) {
      if (!confirm('Êtes-vous sûr de vouloir supprimer cet emplacement ?')) {
        return
      }

      try {
        await axios.delete(`/api/emplacements/${id}`)
        this.emplacements = this.emplacements.filter(e => e.id !== id)
        this.showSuccess('Emplacement supprimé avec succès')
      } catch (error) {
        this.showError('Erreur lors de la suppression')
        console.error(error)
      }
    },

    truncate(text, length) {
      if (!text) return ''
      return text.length > length ? text.substr(0, length) + '...' : text
    },

    showSuccess(message) {
      this.message = message
      this.messageClass = 'alert-success'
      setTimeout(() => this.message = '', 3000)
    },

    showError(message) {
      this.message = message
      this.messageClass = 'alert-danger'
      setTimeout(() => this.message = '', 3000)
    }
  }
}
</script>
