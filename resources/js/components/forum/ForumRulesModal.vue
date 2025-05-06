<template> 
    <Transition name="modal-fade">
        <div v-if="isOpen" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <h2>Conditions et Règles d’un Forum et Groupe de Discussion</h2>
                    <button class="close-button" @click="closeModal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="rules-section">
                        <h3>1. Règles Générales</h3>
                        <p>- Respectez les autres membres : Aucun propos haineux, discriminatoire, offensant ou diffamatoire ne sera toléré.</p>
                        <p>- Pas de spam ni de publicité : Les messages promotionnels non autorisés sont interdits.</p>
                        <p>- Utilisez un langage approprié : Évitez les insultes, la vulgarité ou les comportements toxiques.</p>
                        <p>- Ne publiez pas d’informations personnelles : Pour votre sécurité, ne partagez pas de coordonnées personnelles.</p>
                        <p>- Respectez les décisions des modérateurs : Ils ont le droit de supprimer un message ou de bannir un utilisateur en cas de non-respect des règles.</p>
                    </div>
                    <div class="rules-section">
                        <h3>2. Règles de Publication</h3>
                        <p>- Vérifiez avant de poster : Assurez-vous que votre question ou discussion n’a pas déjà été traitée.</p>
                        <p>- Soyez clair et concis : Rédigez des titres explicites et des messages bien structurés.</p>
                        <p>- Publiez dans les bonnes catégories : Chaque sujet doit être posté dans la section appropriée.</p>
                        <p>- Évitez le contenu inapproprié : Pas de contenu illégal, pornographique, violent ou incitant à la haine.</p>
                    </div>
                    <div class="rules-section">
                        <h3>3. Règles pour les Groupes de Discussion</h3>
                        <p>- Le créateur du groupe est responsable de son bon fonctionnement.</p>
                        <p>- Un groupe peut être public ou privé, selon les préférences du créateur.</p>
                        <p>- Les participants doivent respecter les règles générales du forum.</p>
                        <p>- Toute demande d’adhésion à un groupe privé doit être validée par un administrateur.</p>
                        <p>- Les publications doivent rester en accord avec le thème du groupe.</p>
                    </div>
                    <div class="rules-section">
                        <h3>4. Modération et Sanctions</h3>
                        <p>- Avertissement en cas de non-respect des règles.</p>
                        <p>- Suppression de contenu jugé inapproprié.</p>
                        <p>- Suspension temporaire ou bannissement en cas de récidive.</p>
                        <p>- Suppression du compte en cas de non-respect grave des conditions.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-accept" @click="acceptRules">J'accepte les règles</button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    isOpen: Boolean
});

const emit = defineEmits(['close', 'accept']);

const closeModal = () => emit('close');
const acceptRules = () => {
    emit('accept');
    closeModal();
};
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999; /* Augmenté de 1000 à 9999 */
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    position: relative; /* Ajouté */
    z-index: 10000; /* Ajouté, plus élevé que modal-overlay */
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    text-align: center;
}

.rules-section {
    margin-bottom: 20px;
}

.rules-section h3 {
    color: var(--primary);
    margin-bottom: 10px;
}

.btn-accept {
    background: var(--primary);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-accept:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.close-button {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

/* Animations */
.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: all 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
</style>