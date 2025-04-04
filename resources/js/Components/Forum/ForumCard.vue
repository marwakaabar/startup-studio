<template>
    <BaseForumCard :forum="forum" :current-user="currentUser" :is-my-forum="true" />
</template>

<script>
import BaseForumCard from './BaseForumCard.vue';

export default {
    components: {
        BaseForumCard
    },
    props: {
        forum: {
            type: Object,
            required: true
        },
        currentUser: {
            type: Object,
            required: true
        }
    }
};
</script>

<style scoped>
.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.author-avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e0e0e0;
    display: inline-block;
}
/* Ajoute ici les styles si nécessaire */

</style>
