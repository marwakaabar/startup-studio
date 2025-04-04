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
                        <p>- Tout sujet doit respecter les règles générales du forum et du groupe.</p>
                        <p>- Aucun contenu offensant, haineux, discriminatoire ou illégal n'est autorisé.</p>
                        <p>- La langue utilisée doit être respectueuse et compréhensible.</p>
                        <p>- Le spam et les contenus promotionnels sont interdits.</p>
                    </div>
                    <div class="rules-section">
                        <h3>2. Création sujets</h3>
                        <p>- Le titre du sujet doit être clair et précis.</p>
                        <p>- Chaque sujet doit être en lien avec la thématique du groupe.</p>
                        <p>- Avant de créer un sujet, vérifiez qu'un sujet similaire n'existe pas déjà.</p>
                        <p>- Les messages doivent être bien structurés et compréhensibles.</p>
                    </div>
                    <div class="rules-section">
                        <h3>3. Contenu Autorisé et Interdit</h3>
                        <p>- Autorisé : discussions constructives, partages d'expériences et d'informations pertinentes.</p>
                        <p>- Interdit : contenu illégal, fake news, incitation à la violence ou au harcèlement.</p>
                        <p>- Respect des droits d’auteur : citer les sources lorsque nécessaire.</p>
                    </div>
                    <div class="rules-section">
                        <h3>4. Modération et Sanctions</h3>
                        <p>- Le modérateur et l'administrateur peuvent modifier ou supprimer un sujet non conforme.</p>
                        <p>- Un avertissement peut être donné en cas de non-respect des règles.</p>
                        <p>- Un utilisateur peut être suspendu ou banni en cas de récidive.</p>
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
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
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