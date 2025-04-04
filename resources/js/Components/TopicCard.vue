<template>
    <div class="topic-card">
        <h3>{{ topic.title }}</h3>
        <p>{{ topic.content }}</p>
        <Link :href="'/topics/' + topic.id">Voir</Link>
    </div>
</template>

<script>
export default {
    props: {
        topic: Object,
    },
};
</script>
