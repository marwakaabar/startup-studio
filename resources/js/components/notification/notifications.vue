<template>
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow btn-top" href="javascript:void(0);" 
           data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"
           @click="showNotifications = !showNotifications">
            <i class="ti ti-bell ti-md text-dark"></i>
            <span v-if="unreadCount > 0" class="badge bg-danger rounded-pill badge-notifications">{{ unreadCount }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end notificationBox py-0">
            <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                    <h5 class="text-body mb-0 me-auto text-primary title-notif">Notifications</h5>
                </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                    <li v-if="displayedNotifications.length === 0" class="list-group-item list-group-item-action dropdown-notifications-item text-center">
                        <p class="text-gray-500 mb-0">Aucune notification</p>
                    </li>
                    <li class="list-group-item list-group-item-action dropdown-notifications-item" 
                        v-for="notification in displayedNotifications" 
                        :key="notification.id"
                        @click="markAsRead(notification)"
                        :class="{ 'bg-light': !notification.read_at }">
                        
                        <!-- Participation Request Notification -->
                        <div v-if="notification.data && notification.data.type === 'participation_request'">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ notification.data.requester_name }}</h6>
                                    <p class="mb-0">
                                        demande à rejoindre le forum "{{ notification.data.forum_name }}"
                                    </p>
                                    <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                                </div>
                            </div>
                            <div v-if="!notification.read_at" class="mt-2 d-flex gap-2 justify-content-end">
                                <button 
                                    @click.stop="handleParticipationResponse(notification, 'accepted')"
                                    class="btn btn-sm btn-success"
                                >
                                    <i class="ti ti-check me-1"></i> Accepter
                                </button>
                                <button 
                                    @click.stop="handleParticipationResponse(notification, 'rejected')"
                                    class="btn btn-sm btn-danger"
                                >
                                    <i class="ti ti-x me-1"></i> Refuser
                                </button>
                            </div>
                        </div>
                        
                        <!-- Participation Response Notification -->
                        <div v-else-if="notification.data && notification.data.type === 'participation_response'">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Réponse du forum</h6>
                                    <p class="mb-0">
                                        Votre demande pour "{{ notification.data.forum_name }}" a été 
                                        {{ notification.data.status === 'accepted' ? 'acceptée' : 'refusée' }}
                                    </p>
                                    <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Topic Activity Notification -->
                        <div v-else-if="notification.data && notification.data.type === 'topic_activity'"
                             @click="window.location.href = `/topics/${notification.data.topic_id}`">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">Nouvelle activité dans un topic suivi</h6>
                                    <p class="mb-0">
                                        {{ notification.data.author_name }} a ajouté une 
                                        {{ notification.data.activity_type === 'new_post' ? 'réponse' : 'commentaire' }}
                                        au topic "{{ notification.data.topic_title }}"
                                    </p>
                                    <p class="text-muted small">{{ notification.data.content_preview }}...</p>
                                    <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Warning Notification -->
                        <div v-else-if="notification.data && notification.data.type === 'warning_received'">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-warning">Avertissement reçu</h6>
                                    <p class="mb-0">
                                        Vous avez reçu un avertissement pour la raison suivante : {{ notification.data.reason }}
                                    </p>
                                    <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content Deleted Notification -->
                        <div v-else-if="notification.data && notification.data.type === 'content_deleted'">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-success">Contenu supprimé</h6>
                                    <p class="mb-0">
                                        Le contenu que vous avez signalé a été supprimé par un administrateur
                                    </p>
                                    <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content Author Warning Notification -->
                        <div v-else-if="notification.data && notification.data.type === 'content_author_warning'">
                        <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-danger">Contenu supprimé</h6>
                                    <p class="mb-0">
                                        {{ notification.data.message }}
                                    </p>
                                    <p class="small text-muted mt-1" v-if="notification.data.is_first_warning">
                                        Veuillez prendre note que si cela se reproduit, votre compte pourrait être désactivé.
                                    </p>
                                    <p class="small text-muted mt-1" v-else>
                                        Attention : Ceci est votre dernier avertissement avant la désactivation du compte.
                                    </p>
                                    <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Default Notification -->
                        <div v-else class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <div class="icon" v-html="getNotificationIcon(notification)"></div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ notification.type }}</h6>
                                <p class="mb-0">{{ JSON.stringify(notification.data) }}</p>
                                <small class="text-muted">{{ getTimeAgo(notification.created_at) }}</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
            
            <!-- Show More Button -->
            <li v-if="notifications.length > NOTIFICATIONS_PER_PAGE && !showAll" 
                class="dropdown-menu-footer border-top d-flex justify-content-center pt-2 pb-2">
                <button @click="showAll = true"
                     class="btn btn-link text-primary">
                    Voir plus ({{ notifications.length - NOTIFICATIONS_PER_PAGE }})
                </button>
            </li>
            
            <!-- Footer Link-->
            <li class="dropdown-menu-footer border-top d-flex justify-content-center pt-3 pb-3">
                <a href="/notifications"
                    class="dropdown-item btn-show d-flex justify-content-center text-primary p-2 h-px-40 mb-1 align-items-center">
                    Voir toutes les notifications
                </a>
            </li> 
        </ul>
    </li>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import axios from 'axios';
import 'bootstrap';
import '@popperjs/core';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const notifications = ref([]);
const unreadCount = ref(0);
const showNotifications = ref(false);

const NOTIFICATIONS_PER_PAGE = 5;
const showAll = ref(false);
const displayedNotifications = computed(() => {
    const sortedNotifications = [...notifications.value].sort((a, b) => {
        return new Date(b.created_at) - new Date(a.created_at);
    });
    return showAll.value ? sortedNotifications : sortedNotifications.slice(0, NOTIFICATIONS_PER_PAGE);
});

const icons = {
                daily: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 27 30" fill="none">
                <path d="M12.1111 27.5263H3.77778C3.04107 27.5263 2.33453 27.2158 1.81359 26.663C1.29266 26.1103 1 25.3606 1 24.5789V6.89474C1 6.11304 1.29266 5.36337 1.81359 4.81063C2.33453 4.25789 3.04107 3.94737 3.77778 3.94737H20.4444C21.1812 3.94737 21.8877 4.25789 22.4086 4.81063C22.9296 5.36337 23.2222 6.11304 23.2222 6.89474V13.5263M17.6667 1V6.89474M6.55556 1V6.89474M1 12.7895H23.2222M26 29C26 28.2183 25.7073 27.4686 25.1864 26.9159C24.6655 26.3632 23.9589 26.0526 23.2222 26.0526H20.4444C19.7077 26.0526 19.0012 26.3632 18.4803 26.9159C17.9593 27.4686 17.6667 28.2183 17.6667 29M19.0556 21.6316C19.0556 22.4133 19.3482 23.1629 19.8691 23.7157C20.3901 24.2684 21.0966 24.5789 21.8333 24.5789C22.57 24.5789 23.2766 24.2684 23.7975 23.7157C24.3185 23.1629 24.6111 22.4133 24.6111 21.6316C24.6111 20.8499 24.3185 20.1002 23.7975 19.5475C23.2766 18.9947 22.57 18.6842 21.8333 18.6842C21.0966 18.6842 20.3901 18.9947 19.8691 19.5475C19.3482 20.1002 19.0556 20.8499 19.0556 21.6316Z" stroke="#7E57C2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`,
                rendezVous: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#7E57C2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M11.795 21H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"/><path d="M14 18a4 4 0 1 0 8 0a4 4 0 1 0-8 0m1-15v4M7 3v4m-4 4h16"/><path d="M18 16.496V18l1 1"/></g></svg>`,
                task: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#7E57C2" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" color="currentColor"><path d="M3.5 9.368c0-3.473 0-5.21 1.025-6.289S7.2 2 10.5 2h3c3.3 0 4.95 0 5.975 1.08C20.5 4.157 20.5 5.894 20.5 9.367v5.264c0 3.473 0 5.21-1.025 6.289S16.8 22 13.5 22h-3c-3.3 0-4.95 0-5.975-1.08C3.5 19.843 3.5 18.106 3.5 14.633zM13.5 11H17"/><path d="M7 12s.5 0 1 1c0 0 1.588-2.5 3-3m2.5 7H17M8 2l.082.493c.2 1.197.3 1.796.72 2.152C9.22 5 9.827 5 11.041 5h1.917c1.213 0 1.82 0 2.24-.355c.42-.356.52-.955.719-2.152L16 2M8 17h1"/></g></svg>`,
                conge: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#7E57C2" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" color="currentColor"><path d="M12 15v1.5M3 11l.153 2.863c.164 3.614.246 5.42 1.406 6.529S7.527 21.5 11.145 21.5h1.71c3.618 0 5.427 0 6.586-1.108s1.242-2.915 1.406-6.529L21 11"/><path d="M2.847 10.443C4.547 13.674 8.38 15 12 15s7.453-1.326 9.153-4.557C21.964 8.901 21.35 6 19.352 6H4.648c-1.998 0-2.612 2.9-1.8 4.443M16 6l-.088-.31c-.44-1.54-.66-2.31-1.184-2.75s-1.22-.44-2.611-.44h-.234c-1.391 0-2.087 0-2.61.44c-.525.44-.745 1.21-1.185 2.75L8 6"/></g></svg>`,
    warning: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="#7E57C2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M5.07 19H19a2 2 0 0 0 1.75-2.97L13.74 4.99a2 2 0 0 0-3.5 0L3.25 16.03a2 2 0 0 0 1.75 2.97"/></svg>`,
    content: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#7E57C2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M14 3v4a1 1 0 0 0 1 1h4"/><path d="M17 21H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7l5 5v11a2 2 0 0 1-2 2zm-8-9h6m-6 4h6"/></g></svg>`,
    forum: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#7E57C2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M3 13h18M5 17h14M11 3h2m-1 4v4"/><circle cx="12" cy="19" r="2"/></g></svg>`,
                default: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2a7 7 0 00-7 7c0 5-2 6-2 6h18s-2-1-2-6a7 7 0 00-7-7z" stroke="#7E57C2" stroke-width="2"/>
                    <path d="M9 19h6M10 22h4" stroke="black" stroke-width="2"/>
                </svg>`
};

onMounted(() => {
    if (props.user) {
        loadNotifications();
        
        if (window.Echo) {
            window.Echo.private(`App.Models.User.${props.user.id}`)
                .notification((notification) => {
                    console.log('Notification reçue:', notification);

                    const notificationType = notification.type.split('\\').pop();
                    let notificationData = {
                        id: notification.id,
                        created_at: new Date(),
                        read_at: null,
                        type: notificationType,
                        data: notification.data
                    };

                    // Ajouter la notification à la liste
                    notifications.value = [notificationData, ...notifications.value];
                    unreadCount.value++;

                    // Afficher le toast avec le bon type et message
                    let toastType = 'info';
                    let message = '';

                    switch (notificationType) {
                        case 'ContentAuthorNotification':
                            toastType = 'warning';
                            message = notification.data.message;
                            break;
                        case 'ContentDeletedNotification':
                            toastType = 'success';
                            message = `Le contenu signalé (${notification.data.content_type}) a été supprimé`;
                            break;
                        case 'NewTopicActivityNotification':
                            message = `${notification.data.author_name} a ajouté une ${notification.data.activity_type === 'new_post' ? 'réponse' : 'commentaire'} au topic "${notification.data.topic_title}"`;
                            break;
                        case 'ParticipationRequestNotification':
                            message = `${notification.data.requester_name} demande à rejoindre votre forum "${notification.data.forum_name}"`;
                            toastType = 'request';
                            break;
                        case 'ParticipationResponseNotification':
                            message = `Votre demande pour le forum "${notification.data.forum_name}" a été ${notification.data.status === 'accepted' ? 'acceptée' : 'refusée'}`;
                            toastType = notification.data.status === 'accepted' ? 'success' : 'error';
                            break;
                        case 'WarningNotification':
                            message = `Vous avez reçu un avertissement pour la raison suivante : ${notification.data.reason}`;
                            toastType = 'warning';
                            break;
                    }

                    showToast({
                        type: toastType,
                        data: { message }
                    });
                });
        }
    }
    
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const handleClickOutside = (event) => {
    const dropdown = document.querySelector('.dropdown-menu');
    const button = document.querySelector('.btn-top');
    if (dropdown && !dropdown.contains(event.target) && !button.contains(event.target)) {
        showNotifications.value = false;
    }
};

const loadNotifications = async () => {
    try {
        const response = await axios.get('/notifications');
        notifications.value = response.data;
        unreadCount.value = notifications.value.filter(n => !n.read_at).length;
    } catch (error) {
        console.error('Erreur lors du chargement des notifications:', error);
    }
};

const getNotificationIcon = (notification) => {
    const type = notification.type;
    
    if (type === 'ContentAuthorNotification' || type === 'ContentDeletedNotification') {
        return icons.content;
    } else if (type === 'WarningNotification') {
        return icons.warning;
    } else if (type === 'NewTopicActivityNotification') {
        return icons.forum;
    } else if (type === 'ParticipationRequestNotification' || type === 'ParticipationResponseNotification') {
        return icons.forum;
    }
    
    return icons.default;
};

const showToast = (notification) => {
    let message = notification.data.message || '';
    let type = notification.type || 'info';

    const toast = document.createElement('div');
    toast.className = `notification-toast ${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => toast.remove(), 300);
    }, 4700);
};

const markAsRead = async (notification) => {
    if (notification.read_at) return;
    
    try {
        await axios.patch(`/notifications/${notification.id}`);
        notification.read_at = new Date();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Erreur lors du marquage de la notification comme lue:', error);
    }
};

const handleParticipationResponse = async (notification, status) => {
    try {
        if (!notification.data.forum_id) {
            console.error('Forum ID manquant dans la notification');
            return;
        }

        await axios.post(`/forums/${notification.data.forum_id}/respond-participation`, {
            status: status
        });
        
        await markAsRead(notification);
        
        const index = notifications.value.findIndex(n => n.id === notification.id);
        if (index !== -1) {
            notifications.value.splice(index, 1);
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
        
        const actionText = status === 'accepted' ? 'accepté' : 'refusé';
        showToast({
            type: 'success',
            data: {
                message: `Vous avez ${actionText} la demande de participation`
            }
        });
        
    } catch (error) {
        console.error('Erreur lors du traitement de la demande:', error);
        showToast({
            type: 'error',
            data: {
                message: 'Une erreur est survenue lors du traitement de la demande'
            }
        });
    }
};

const getTimeAgo = (timestamp) => {
    if (!timestamp) return '';
    
    const now = new Date();
    const date = new Date(timestamp);
    const seconds = Math.floor((now - date) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval >= 1) {
        return `Il y a ${interval} an${interval > 1 ? 's' : ''}`;
    }
    
    interval = Math.floor(seconds / 2592000);
    if (interval >= 1) {
        return `Il y a ${interval} mois`;
    }
    
    interval = Math.floor(seconds / 86400);
    if (interval >= 1) {
        return `Il y a ${interval} jour${interval > 1 ? 's' : ''}`;
    }
    
    interval = Math.floor(seconds / 3600);
    if (interval >= 1) {
        return `Il y a ${interval} heure${interval > 1 ? 's' : ''}`;
    }
    
    interval = Math.floor(seconds / 60);
    if (interval >= 1) {
        return `Il y a ${interval} minute${interval > 1 ? 's' : ''}`;
    }
    
    return 'À l\'instant';
};
</script>

<style>
.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px;
    background: white;
    border-left: 4px solid #4A90E2;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 9999;
    min-width: 300px;
    animation: slideIn 0.3s ease-out;
    border-radius: 4px;
}

.notification-toast.warning {
    border-left-color: #FFC107;
}

.notification-toast.success {
    border-left-color: #4CAF50;
}

.notification-toast.error {
    border-left-color: #F44336;
}

.notification-toast.request {
    border-left-color: #9C27B0;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.dropdown-menu.notificationBox {
    width: 320px;
    max-height: 500px;
    overflow-y: auto;
}

.scrollable-container {
    max-height: 350px;
    overflow-y: auto;
}
</style>