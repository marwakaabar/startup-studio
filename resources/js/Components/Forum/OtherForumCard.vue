<template>
    <BaseForumCard :forum="forum" :current-user="currentUser" :is-my-forum="false" />
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
