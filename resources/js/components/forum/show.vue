<template>
    <div class="forum-page">
        <div class="page-header">
            <h2 class="section-title">Actualité</h2>
            <h2 class="section-title">Liste des Sujets</h2>

            <button @click="createNewTopic" class="new-topic-btn">
                Nouveau Sujet +
            </button>
        </div>

        <div class="forum-content">
            <!-- Left Column: News -->
            <div class="news-section">
                <!-- Recent Topics Section -->
                <div class="content-box">
                    <h3 class="subsection-title"><svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.5116 21.9794H3.57285C3.15889 21.9795 2.75423 21.8567 2.41004 21.6268C2.06584 21.3968 1.79758 21.0699 1.63917 20.6875C1.48077 20.305 1.43934 19.8842 1.52012 19.4782C1.60091 19.0722 1.80028 18.6993 2.09302 18.4066L2.57947 17.9201C0.916704 15.9944 0.00126063 13.5353 0 10.991C0.074266 8.00897 1.32758 5.17793 3.48534 3.11825C5.64309 1.05856 8.52931 -0.0618062 11.5116 0.00263354C14.4939 -0.0618062 17.3801 1.05856 19.5378 3.11825C21.6956 5.17793 22.9489 8.00897 23.0232 10.991C22.9489 13.9731 21.6956 16.8041 19.5378 18.8638C17.3801 20.9235 14.4939 22.0439 11.5116 21.9794ZM11.5116 2.09566C9.08432 2.03101 6.73033 2.9308 4.96505 4.59802C3.19977 6.26524 2.16705 8.56403 2.09302 10.991C2.09678 12.1516 2.33546 13.2993 2.7947 14.3651C3.25393 15.4309 3.92419 16.3927 4.7651 17.1925C4.86464 17.2891 4.94398 17.4045 4.99849 17.5321C5.05299 17.6597 5.08159 17.7968 5.0826 17.9356C5.08361 18.0743 5.05703 18.2118 5.00439 18.3402C4.95176 18.4685 4.87412 18.5851 4.776 18.6832L3.57279 19.8864H11.5116C13.9389 19.9511 16.2928 19.0513 18.0581 17.3841C19.8234 15.7168 20.8561 13.4181 20.9302 10.991C20.8561 8.56403 19.8234 6.26524 18.0581 4.59802C16.2928 2.9308 13.9389 2.03101 11.5116 2.09566Z" fill="#0086D9"/>
</svg>
Les derniers sujets</h3>
                    <div class="content-list">
                        <div v-for="topic in recentTopics" :key="topic.id" class="content-item">
                            <p class="content-text">{{ topic.content }}</p>
                            <div class="content-divider"></div>
                        </div>
                    </div>
                </div>

                <!-- Recent Comments Section -->
                <div class="content-box">
                    <h3 class="subsection-title"><svg width="30" height="25" viewBox="0 0 30 25" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M27.5 0H11.5C10.12 0 9 1.12 9 2.5V5H2.5C1.12 5 0 6.12 0 7.5V19.5C0 20.88 1.12 22 2.5 22H3.95C4.095 22 4.23 22.06 4.325 22.165C4.42 22.27 4.465 22.405 4.45 22.54C4.335 23.685 4.055 24.275 4.055 24.275C3.97 24.445 3.985 24.645 4.1 24.795C4.195 24.925 4.345 25 4.5 25C4.525 25 4.555 25 4.58 24.995C4.69 24.975 7.285 24.52 7.885 22.38C7.945 22.155 8.14 22 8.365 22H18.5C19.88 22 21 20.88 21 19.5V17H21.635C21.86 17 22.055 17.155 22.12 17.38C22.71 19.52 25.31 19.975 25.42 19.995C25.445 20 25.47 20 25.5 20C25.655 20 25.805 19.925 25.9 19.795C26.01 19.645 26.03 19.445 25.95 19.28C25.945 19.27 25.665 18.69 25.55 17.54C25.54 17.405 25.58 17.27 25.675 17.165C25.77 17.06 25.91 17 26.05 17H27.5C28.88 17 30 15.88 30 14.5V2.5C30 1.12 28.88 0 27.5 0ZM20 19.5C20 20.325 19.325 21 18.5 21H8.365C7.695 21 7.1 21.455 6.92 22.11C6.675 22.985 5.89 23.48 5.265 23.745C5.335 23.445 5.4 23.075 5.445 22.64C5.485 22.225 5.35 21.81 5.065 21.495C4.78 21.18 4.375 21 3.95 21H2.5C1.675 21 1 20.325 1 19.5V7.5C1 6.675 1.675 6 2.5 6H18.5C19.325 6 20 6.675 20 7.5V19.5Z" fill="#0086D9"/>
</svg>
Les derniers commentaires</h3>
                    <div class="content-list">
                        <div v-for="comment in recentComments" :key="comment.id" class="content-item">
                            <p class="content-text">{{ comment.content }}</p>
                            <div class="content-divider"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Topics -->
            <div class="topics-section">
                <div class="filters-bar">
                    <span v-for="filter in filters" 
                          :key="filter.value"
                          @click="changeFilter(filter.value)"
                          :class="['filter-option', { active: currentFilter === filter.value }]">
                          
                          <template v-if="filter.value === 'all'">
                              <svg width="14" height="9" viewBox="0 0 14 9" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 5px;">
                                  <path d="M8.58958 8.63021L8.23185 8.81537L8.58958 9L8.94731 8.81537L8.58958 8.63021ZM0.505269 0C0.371264 0 0.242746 0.0275531 0.14799 0.0765979C0.0532337 0.125643 0 0.192162 0 0.261521C0 0.330881 0.0532337 0.3974 0.14799 0.446445C0.242746 0.49549 0.371264 0.523043 0.505269 0.523043V0ZM3.17915 6.20015L8.23185 8.81537L8.94731 8.44505L3.89462 5.82984L3.17915 6.20015ZM8.94731 8.81537L14 6.20015L13.2845 5.82984L8.23185 8.44505L8.94731 8.81537ZM9.09485 8.63021V3.39978H8.08431V8.63021H9.09485ZM2.52635 0H0.505269V0.523043H2.52635V0ZM9.09485 3.39978C9.09485 2.4981 8.40281 1.63335 7.17098 0.995772C5.93914 0.35819 4.26842 0 2.52635 0V0.523043C4.00041 0.523043 5.4141 0.826127 6.45642 1.36562C7.49874 1.90511 8.08431 2.63682 8.08431 3.39978H9.09485Z" fill="black"/>
                              </svg>
                          </template>

                          {{ filter.label }}
                    </span>
                </div>
                <div class="topics-list">
                    <div v-for="topic in filteredTopics" :key="topic.id" :class="$style.frameParent">
                        <div :class="$style.rectangleParent">
                            <div :class="$style.topicIcon">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div :class="$style.sujetN1Parent">
                                <b :class="$style.sujetN1" @click="navigateToTopic(topic.id)" style="cursor: pointer">
                                    {{ topic.title }}
                                </b>
                                <div :class="$style.dateDeCration">
                                    Date de création : {{ formatDate(topic.created_at) }}
                                </div>
                                <div :class="$style.crePar">
                                    Créé par : {{ topic.user?.name }}
                                </div>
                                <div :class="$style.statsGroup">
                                    <div :class="$style.userAvatars">
                                        <img v-for="user in topic.recent_users" 
                                             :key="user.id" 
                                             :src="user.image" 
                                             :alt="user.name"
                                             :class="$style.userAvatar" />
                                    </div>
                                    <!-- Stats component selon l'image -->
                                    <div :class="$style.groupParent">
                                        <div :class="$style.statItem">
                                            <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect width="12" height="11" fill="#F5F5F5"/>
<g id="sujet" clip-path="url(#clip0_14_4209)">
<rect width="1920" height="1273" transform="translate(-1688 -455)" fill="#F1F9FF"/>
<g id="Frame 454">
<g id="Frame 450">
<g id="Frame 441" filter="url(#filter0_d_14_4209)">
<rect x="-689" y="-95" width="776" height="116" rx="11" fill="white" shape-rendering="crispEdges"/>
</g>
<g id="Frame 426">
<path id="Vector" fill-rule="evenodd" clip-rule="evenodd" d="M3.73713 0.500511H8.26343C9.02637 0.500511 9.64197 0.500511 10.1259 0.560315C10.6282 0.621652 11.0512 0.754549 11.3872 1.06175C11.7232 1.36945 11.8672 1.7569 11.9353 2.21693C12 2.66009 12 3.22388 12 3.92261V5.2567C12 5.95543 12 6.51922 11.9353 6.96238C11.8672 7.42241 11.7232 7.80986 11.3872 8.11757C11.0512 8.42527 10.6282 8.55715 10.1259 8.61951C9.64197 8.6788 9.02637 8.6788 8.26343 8.6788H4.55477C4.36501 8.6788 4.29971 8.6788 4.2411 8.68493C3.91905 8.71502 3.62213 8.85786 3.41231 9.08363C3.3738 9.12554 3.3364 9.1741 3.22869 9.31773L3.13158 9.44705C3.00042 9.62186 2.89773 9.75885 2.81736 9.86056C2.74145 9.95768 2.66611 10.0482 2.59132 10.1126C1.72122 10.869 0.282405 10.4601 0.0334869 9.3852C0.0130605 9.27786 0.00297565 9.16908 0.0033487 9.06011C2.33903e-08 8.93437 0 8.76876 0 8.55664V3.9221C0 3.22337 5.82158e-08 2.65958 0.0652993 2.21642C0.132273 1.75639 0.277382 1.36894 0.612809 1.06124C0.948793 0.753527 1.37184 0.621652 1.87415 0.559804C2.35803 0.5 2.97363 0.5 3.73657 0.5L3.73713 0.500511ZM1.98633 1.31987C1.57667 1.37048 1.36012 1.46299 1.20553 1.60407C1.05093 1.74566 0.950467 1.94398 0.895214 2.31967C0.838845 2.70456 0.837729 3.21673 0.837729 3.95073V8.55101C0.837729 8.76927 0.837729 8.92671 0.840519 9.04222C0.843868 9.16592 0.850007 9.21294 0.852798 9.22623C0.878593 9.33738 0.936284 9.44021 1.01992 9.52413C1.10357 9.60806 1.21013 9.67003 1.32864 9.70368C1.44714 9.73732 1.5733 9.74141 1.6941 9.71553C1.81491 9.68965 1.92599 9.63473 2.01591 9.55643C2.02651 9.54723 2.06056 9.51196 2.13925 9.41178C2.21348 9.31773 2.31059 9.18841 2.44565 9.00798L2.5517 8.8669C2.64267 8.74576 2.70518 8.66244 2.77494 8.58782C3.12446 8.21116 3.61936 7.97272 4.15627 7.92231C4.26287 7.91209 4.37394 7.91209 4.53411 7.91209H8.23273C9.03363 7.91209 9.5923 7.91106 10.0142 7.85944C10.4239 7.80883 10.6404 7.71632 10.795 7.57524C10.9496 7.43366 11.0501 7.23533 11.1053 6.85964C11.1617 6.47373 11.1628 5.96207 11.1628 5.22858V3.95073C11.1628 3.21724 11.1617 2.70507 11.1053 2.31916C11.0501 1.94398 10.9491 1.74566 10.795 1.60407C10.6404 1.46248 10.4239 1.37048 10.0137 1.31987C9.5923 1.26825 9.03363 1.26723 8.23273 1.26723H3.76782C2.96693 1.26723 2.4077 1.26825 1.98633 1.31987Z" fill="black"/>
</g>
</g>
</g>
</g>
<defs>
<filter id="filter0_d_14_4209" x="-699" y="-107" width="816" height="156" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
<feFlood flood-opacity="0" result="BackgroundImageFix"/>
<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
<feOffset dx="10" dy="8"/>
<feGaussianBlur stdDeviation="10"/>
<feComposite in2="hardAlpha" operator="out"/>
<feColorMatrix type="matrix" values="0 0 0 0 0.317647 0 0 0 0 0.513726 0 0 0 0 0.25 0"/>
<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_14_4209"/>
<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_14_4209" result="shape"/>
</filter>
<clipPath id="clip0_14_4209">
<rect width="1920" height="1273" fill="white" transform="translate(-1688 -455)"/>
</clipPath>
</defs>
</svg>
                                            <div :class="$style.statNumber">{{ topic.posts_count }}</div>
                                        </div>
                                        <div :class="$style.statItem">
                                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
<path id="like" d="M0 0H19V19H0V0Z" fill="url(#pattern0_14_16631)"/>
<defs>
<pattern id="pattern0_14_16631" patternContentUnits="objectBoundingBox" width="1" height="1">
<use xlink:href="#image0_14_16631" transform="scale(0.00195312)"/>
</pattern>
<image id="image0_14_16631" width="512" height="512" preserveAspectRatio="none" xlink:href="data:image/png;base64,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"/>
</defs>
</svg>
                                            <div :class="$style.statNumber">{{ topic.total_likes }}</div>
                                        </div>
                                        <div :class="$style.statItem">
                                            <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M10 0.75C5.83333 0.75 2.275 3.175 0.416666 6.75C2.275 10.325 5.83333 12.75 10 12.75C14.1667 12.75 17.725 10.325 19.5833 6.75C17.725 3.175 14.1667 0.75 10 0.75ZM10 10.75C7.7 10.75 5.83333 8.95833 5.83333 6.75C5.83333 4.54167 7.7 2.75 10 2.75C12.3 2.75 14.1667 4.54167 14.1667 6.75C14.1667 8.95833 12.3 10.75 10 10.75ZM10 4.41667C8.61666 4.41667 7.5 5.45833 7.5 6.75C7.5 8.04167 8.61666 9.08333 10 9.08333C11.3833 9.08333 12.5 8.04167 12.5 6.75C12.5 5.45833 11.3833 4.41667 10 4.41667Z" fill="black"/>
                                            </svg>
                                            <div :class="$style.statNumber">{{ topic.views_count }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
    props: {
        forumId: {
            type: [Number, String],
            required: true
        }
    },

    data() {
        return {
            forum: {},
            topics: [],
            recentTopics: [],
            recentComments: [],
            currentFilter: 'all',
            filters: [
                { label: 'Tous', value: 'all' },
                { label: 'Récents', value: 'recent' },
                { label: 'Populaires', value: 'popular' }
            ],
            defaultAvatar: 'path/to/default/avatar.png',
            loading: false
        }
    },

    computed: {
        filteredTopics() {
            switch(this.currentFilter) {
                case 'recent':
                    return [...this.topics].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                case 'popular':
                    return [...this.topics].sort((a, b) => b.views_count - a.views_count);
                default:
                    return this.topics;
            }
        }
    },

    methods: {
        async loadForumData() {
            try {
                const response = await axios.get(`/forums/${this.forumId}/topics`);
                this.topics = response.data.topics;
                this.recentTopics = this.topics.slice(0, 5);
                this.recentComments = [];
            } catch (error) {
                console.error('Error loading forum data:', error);
            }
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        },

        truncateText(text, length) {
            if (!text) return '';
            return text.length > length ? text.substring(0, length) + '...' : text;
        },

        changeFilter(filter) {
            this.currentFilter = filter;
        },

        createNewTopic() {
            window.location.href = `/forums/${this.forumId}/topics/create`;
        },

        navigateToTopic(topicId) {
            window.location.href = `/topics/${topicId}`;
        }
    },

    mounted() {
        this.loadForumData();
    }
};
</script>

<style module>
.frameChild {
    width: 66px;
    position: relative;
    border-radius: 11px;
    background-color: #eaeaea;
    height: 58px;
}

.sujetN1 {
    align-self: stretch;
    position: relative;
    font-size: 25px;
    line-height: 30px;
    display: flex;
    color: #005183;
    align-items: center;
    height: 27px;
    flex-shrink: 0;
}

.dateDeCration {
    align-self: stretch;
    position: relative;
    line-height: 13.2px;
    display: inline-block;
    height: 17px;
    flex-shrink: 0;
}

.crePar {
    align-self: stretch;
    position: relative;
    line-height: 13.2px;
    display: inline-block;
    height: 11px;
    flex-shrink: 0;
}

.sujetN1Parent {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 5px;
}

.rectangleParent {
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 15px;
    width: 100%;
    padding: 0 10px;
}

.frameParent {
    width: 100%;
    position: relative;
    margin-bottom: 15px;
    box-shadow: 10px 8px 20px rgba(0, 81, 131, 0.25);
    border-radius: 11px;
    background-color: #fff;
    min-height: 116px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 22px 15px;
    box-sizing: border-box;
    text-align: left;
    font-size: 11px;
    color: #0086d9;
    font-family: Poppins;
}

.topicIcon {
    min-width: 66px;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    background-color: rgba(0, 134, 217, 0.1);
    color: #0086d9;
    font-size: 24px;
}

/* Nouveau code pour les stats comme sur l'image */
.statsGroup {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    margin-top: 10px;
}

.userAvatars {
    display: flex;
    align-items: center;
}

.userAvatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: -8px;
    border: 2px solid white;
}

/* Style pour le groupe de statistiques selon l'image */
.groupParent {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    text-align: left;
    font-size: 10px;
    color: #000;
}

.statItem {
    display: flex;
    align-items: center;
    gap: 3px;
}

.vectorIcon {
    width: 12px;
    position: relative;
    height: 10px;
}

.likeIcon {
    width: 12px;
    position: relative;
    height: 12px;
}

.saveIcon {
    width: 14px;
    position: relative;
    height: 10px;
}

.statNumber {
    position: relative;
    letter-spacing: 0.04em;
    line-height: 12px;
    display: inline-block;
    height: 10px;
    flex-shrink: 0;
    min-width: 5px;
    text-align: left;
}
</style>

<style scoped>
.forum-page {
    background-color: #f1f9ff;
    min-height: 100vh;
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.forum-content {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
}

.news-section {
    width: 35%;
    background: white;
    border-radius: 23px;
    padding: 2rem;
    box-shadow: 10px 8px 20px rgba(0, 81, 131, 0.25);
}

.topics-section {
    width: 65%;
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: -2rem; /* Added to align with left card */
}

.main-title {
    font-size: 39px;
    color: #005183;
    text-align: center;
    margin-bottom: 2rem;
}

.section-title {
    font-size: 25px;
    color: #0093ee;
    margin-bottom: 1.5rem;
}

.subsection-title {
    font-size: 16px;
    color: #0086d9;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.content-box {
    margin-bottom: 2rem;
}

.content-item {
    padding: 1rem 0;
}

.content-divider {
    border-top: 1px solid #0068a9;
    margin-top: 1rem;
}

.new-topic-btn {
    border: 1px solid #0093ee;
    color: #0093ee;
    background: none;
    padding: 10px 24px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.new-topic-btn:hover {
    background: #0093ee;
    color: white;
}

.filters-bar {
    display: flex;
    gap: 2rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e0e0e0;
    margin-bottom: 1.5rem;
}

.filter-option {
    color: #005183;
    cursor: pointer;
    font-size: 13px;
    display: flex;
    align-items: center;
}

.filter-option.active {
    font-weight: bold;
    text-decoration: underline;
}
</style>