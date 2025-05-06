<template>
  <div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="/assets/img/dash/circletop.png" alt="" class="circle circletop">
    <img src="/assets/img/dash/circleright.png" alt="" class="circle circleright">
    <img src="/assets/img/dash/circletopright.png" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-5 col-md-6 col-12">
        <div class="card cardLogin">
          <div class="card-body text-center">
            <div class="mb-4">
              <img src="/assets/img/illustrations/email-verification.svg" alt="Email Verification" style="max-width: 200px;">
            </div>
            
            <h4 class="mb-3">Vérification de l'adresse email</h4>
            
            <div v-if="error" class="alert alert-danger alert-dismissible mb-4" role="alert">
              {{ error }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            
            <div v-if="loading" class="mb-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
              <p class="mt-2">Vérification en cours...</p>
            </div>
            
            <div v-if="success" class="mb-4">
              <div class="d-inline-block p-3 bg-success bg-opacity-10 rounded-circle mb-3">
                <i class="iconify text-success fs-1" data-icon="mdi:check-circle"></i>
              </div>
              <h5>Email vérifié avec succès!</h5>
              <p>Votre adresse email a été vérifiée. Vous pouvez maintenant accéder à toutes les fonctionnalités.</p>
              <a href="/dashboard" class="btn btn-primary mt-2">Accéder au tableau de bord</a>
            </div>
            
            <div v-if="!loading && !success && !error" class="mb-4">
              <p>Nous vérifions votre lien de vérification. Veuillez patienter...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VerifyEmailComponent',
  props: {
    id: {
      type: String,
      required: true
    },
    hash: {
      type: String,
      required: true
    },
    expires: {
      type: String,
      required: true
    },
    signature: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      loading: true,
      success: false,
      error: ''
    }
  },
  mounted() {
    this.verifyEmail();
  },
  methods: {
    verifyEmail() {
      const url = `/email/verify/${this.id}`;
      const query = {
        hash: this.hash,
        expires: this.expires,
        signature: this.signature
      };
      
      axios.get(url, { params: query })
        .then(response => {
          this.loading = false;
          if (response.data.status === 'success') {
            this.success = true;
            
            // Rediriger automatiquement après 3 secondes
            setTimeout(() => {
              window.location.href = '/dashboard';
            }, 3000);
          } else {
            this.error = response.data.message || 'Une erreur est survenue lors de la vérification de votre email. Veuillez réessayer.';
          }
        })
        .catch(error => {
          this.loading = false;
          if (error.response && error.response.data && error.response.data.message) {
            this.error = error.response.data.message;
          } else {
            this.error = 'Le lien de vérification est invalide ou a expiré. Veuillez demander un nouveau lien de vérification.';
          }
        });
    }
  }
}
</script>

<style scoped>
/* Vous pouvez conserver les styles existants ou les adapter selon vos besoins */
</style>
