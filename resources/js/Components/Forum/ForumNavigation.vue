<template>
    <div class="forum-navigation-container">
        <button 
            class="nav-button prev" 
            @click="$emit('previous')"
            :disabled="currentIndex === 0"
        >
            <i class="fas fa-chevron-left"></i>
        </button>

        <div class="cards-container">
            <slot></slot>
        </div>

        <button 
            class="nav-button next" 
            @click="$emit('next')"
            :disabled="currentIndex >= totalForums - 1"
        >
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
</template>

<script>
export default {
    props: {
        currentIndex: {
            type: Number,
            required: true
        },
        totalForums: {
            type: Number,
            required: true
        }
    }
}
</script>

<style scoped>
.forum-navigation-container {
    display: flex;
    align-items: center;
    position: relative;
    width: 100%;
}

.cards-container {
    flex: 1;
    overflow: hidden;
}

.nav-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    z-index: 10;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.nav-button.prev {
    left: -20px;
}

.nav-button.next {
    right: -20px;
}

.nav-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
