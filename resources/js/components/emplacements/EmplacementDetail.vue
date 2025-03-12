<template>
  <div class="container">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h2>Détails de l'emplacement</h2>
        <div>
          <router-link :to="{ name: 'emplacements.edit', params: { id: id }}"
                      class="btn btn-warning">
            <i class="fas fa-edit"></i> Modifier
          </router-link>
        </div>
      </div>

      <div class="card-body">
        <div v-if="message" class="alert" :class="messageClass" role="alert">
          {{ message }}
        </div>

        <div v-if="emplacement" class="row">
          <div class="col-md-6">
            <h3>Informations</h3>
            <table class="table">
              <tr>
                <th>Nom :</th>
                <td>{{ emplacement.nom }}</td>
              </tr>
              <tr>
                <th>Établissement :</th>
                <td>{{ emplacement.etablissement.nom }}</td>
              </tr>
              <tr>
                <th>Description :</th>
                <td>{{ emplacement.description || 'Aucune description' }}</td>
              </tr>
            </table>
          </div>

          <div class="col-md-6">
            <h3>Photo</h3>
            <img v-if="emplacement.photo_path"
                 :src="'/storage/' + emplacement.photo_path"
                 :alt="'Photo de ' + emplacement.nom"
                 class="img-fluid rounded">
            <div v-else class="alert alert-info">
              Aucune photo disponible
            </div>
          </div>
        </div>

        <div v-if="emplacement && emplacement.stocks" class="mt-4">
          <h3>Stocks actuels</h3>
          <div v-if="emplacement.stocks.length > 0" class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Fourniture</th>
                  <th>Quantité</th>
                  <th>Seuil d'alerte</th>
                  <th>État</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stock in emplacement.stocks"
                    :key="stock.id"
                    :class="{ 'table-warning': stock.quantite <= stock.seuil_alerte }">
                  <td>{{ stock.fourniture.nom }}</td>
                  <td>{{ stock.quantite }}</td>
                  <td>{{ stock.seuil_alerte }}</td>
                  <td>
                    <span v-if="stock.quantite <= stock.seuil_alerte"
                          class="badge bg-warning">
                      Stock bas
                    </span>
                    <span v-else class="badge bg-success">
                      OK
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="alert alert-info">
            Aucun stock enregistré pour cet emplacement
          </div>
        </div>

        <div class="mt-4">
          <router-link :to="{ name: 'emplacements.index' }" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import axios from 'axios'
import { defineComponent } from 'vue'

interface Stock {
  id: number
  quantite: number
  seuil_alerte: number
  fourniture: {
    id: number
    nom: string
  }
}

interface Emplacement {
  id: number
  nom: string
  description: string
  photo_path: string | null
  etablissement: {
    id: number
    nom: string
  }
  stocks: Stock[]
}

export default defineComponent({
  name: 'EmplacementDetail',

  props: {
    id: {
      type: String,
      required: true
    }
  },

  data() {
    return {
      emplacement: null as Emplacement | null,
      message: '',
      messageClass: ''
    }
  },

  created() {
    this.fetchEmplacement()
  },

  methods: {
    async fetchEmplacement() {
      try {
        const response = await axios.get(`/api/emplacements/${this.id}`)
        this.emplacement = response.data
      } catch (error) {
        this.showError('Erreur lors du chargement de l\'emplacement')
        console.error(error)
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
