<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    routeName: String,
    componentName: String
});
</script>

<template>
    <Link
        :href="route(props.routeName)"
        class="text-gray-700 hover:text-[#4A90E2] font-medium transition-colors duration-200"
        :class="{ 'text-[#4A90E2] border-b-2 border-[#4A90E2]': $page.component === props.componentName }"
    >
        <slot />
    </Link>
</template>
