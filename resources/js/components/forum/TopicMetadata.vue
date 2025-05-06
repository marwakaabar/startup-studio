<template>
    <div class="topic-metadata">
        <MetadataItem icon="calendar" :text="`Créé le ${formatDate(topic.created_at)}`" />
        <MetadataItem icon="user" :text="`Créé par ${topic.user?.name}`" />
        <MetadataItem icon="messages" :text="`${posts.length} Réponses`" />
        <MetadataItem icon="views" :text="`${topic.views_count} Vues`" />
        <!--<MetadataItem 
            icon="last-message" 
            :text="`Dernier message par ${topic.last_post.user?.name} ${formatTimeAgo(topic.last_post.created_at)}`" 
        />
        <MetadataItem icon="hashtags">
            <template #default>
                <span v-for="tag in topic.hashtags" :key="tag.id" class="hashtag">
                    #{{ tag.name }}
                </span>
            </template>
        </MetadataItem>-->
    </div>
</template>

<script>
import { formatDate, formatTimeAgo } from '../../utils/dateFormatters';
import MetadataItem from './MetadataItem.vue';

export default {
    name: 'TopicMetadata',
    components: { MetadataItem },
    props: {
        topic: {
            type: Object,
            required: true
        },
        posts: {
            type: Array,
            required: true
        }
    },
    methods: {
        formatDate,
        formatTimeAgo
    }
}
</script>

<style scoped>
.topic-metadata {
    display: flex;
    gap: 11px;
    align-items: center;
    margin-bottom: 2rem;
}

.hashtag {
    background-color: #e1f0ff;
    color: #0093ee;
    padding: 4px 8px;
    border-radius: 4px;
    margin-right: 8px;
    font-size: 12px;
}
</style>
