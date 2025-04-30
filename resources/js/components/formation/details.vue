<template>
    <div class="row">
        <div class="col-12 mb-5">
            <div class="card card-1 cardDetails">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-12 left">
                            <img :src="formation.image" alt="" class="image">
                            <div>
                                <h5>{{ formation.nom }}</h5>
                                <div v-if="formation.statut === 'oui'"
                                    class="statut text-warning d-flex align-items-center justify-content-center">
                                    <img src="/assets/img/dash/star2.png" alt="" class="me-2"> Formation certifiante
                                </div>
                                <div v-else-if="formation.statut === 'non'"
                                    class="statut text-info d-flex align-items-center justify-content-center">
                                    <img src="/assets/img/dash/info.png" alt="" class="me-2">Formation non certifiante
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-12 right">
                            <h4>{{ formation.price }}€</h4>
                            <a href="" class="btn btn-primary fs-sm">
                                Modifier
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row filters">
        <div class="col-12 mb-4">
            <h2 class="title">Statistique d’inscription</h2>
        </div>
        <div class=" col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-start align-items-center">
            <div class="input-group mb-4 me-3">
                <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M17.8749 18L12.4244 12.3333M1.52344 7.61111C1.52344 8.47929 1.68792 9.33898 2.00748 10.1411C2.32705 10.9432 2.79544 11.672 3.38592 12.2859C3.9764 12.8998 4.6774 13.3867 5.4489 13.719C6.22039 14.0512 7.04728 14.2222 7.88234 14.2222C8.71741 14.2222 9.54429 14.0512 10.3158 13.719C11.0873 13.3867 11.7883 12.8998 12.3788 12.2859C12.9692 11.672 13.4376 10.9432 13.7572 10.1411C14.0768 9.33898 14.2412 8.47929 14.2412 7.61111C14.2412 6.74293 14.0768 5.88325 13.7572 5.08115C13.4376 4.27905 12.9692 3.55025 12.3788 2.93635C11.7883 2.32245 11.0873 1.83548 10.3158 1.50324C9.54429 1.171 8.71741 1 7.88234 1C7.04728 1 6.22039 1.171 5.4489 1.50324C4.6774 1.83548 3.9764 2.32245 3.38592 2.93635C2.79544 3.55025 2.32705 4.27905 2.00748 5.08115C1.68792 5.88325 1.52344 6.74293 1.52344 7.61111Z"
                            stroke="#C6C6C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <input type="text" class="form-control" placeholder="Chercher">
            </div>
            <select name="" id="" class="form-select me-3 mb-4">
                <option value="" selected disabled>Domaine</option>
            </select>
        </div>
    </div>

    <div class=" mb-5">
        <div class="col-12 table-responsive px-2">
            <table id="startupsTable" class="table table-1 table-2 w-100">
                <thead>
                    <tr>
                        <th class="w-25">Startup</th>
                        <th class="w-25">Présence</th>
                        <th class="w-25">Performance</th>
                        <th class="w-25">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in startups" :key="item.id">
                        <td>
                            <div class="media media-2">
                                <img :src="item.logo" alt="" class="rounded-circle">
                            </div>
                        </td>
                        <td>
                            <div class="rating">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star"></i>
                                <i class="bi bi-star"></i>
                            </div>
                        </td>


                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span><strong>{{ item.performances }}%</strong></span>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                        :style="{ width: item.performances + '%' }" :aria-valuenow="item.performances"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>


                        </td>
                        <td>
                            <span class="badge text-white" :class="getNoteClass(item.note)">
                                <strong>{{ item.note }}/20</strong>
                            </span>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row filters">
        <div
            class="col-lg-8 col-md-8 col-sm-8 col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-start align-items-center">
            <h2 class="title mb-4">Cours de la formation</h2>
        </div>
        <div
            class="col-lg-4 col-md-4 col-sm-4 col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-end align-items-center">
            <a href="" data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-primary mb-4">
                Modifier
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="accordion mt-3" id="accordionExample">
                <div class="card accordion-item mb-4" v-for="(module, moduleIndex) in modules" :key="module.id">
                    <h2 class="accordion-header" :id="'heading' + moduleIndex">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            :data-bs-target="'#collapse' + moduleIndex" :aria-controls="'collapse' + moduleIndex">
                            Module {{ moduleIndex + 1 }} : {{ module.name }}
                        </button>
                    </h2>

                    <div :id="'collapse' + moduleIndex" class="accordion-collapse collapse"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <div class="box mb-4" v-for="(lesson, lessonIndex) in module.lessons" :key="lesson.id">
                                <h5>Leçon {{ lessonIndex + 1 }} : {{ lesson.title }}</h5>
                                <div class="cursor-pointer w-100 mb-3">
                                    <video class="w-100"
                                        poster="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.jpg"
                                        id="plyr-video-player" playsinline controls>
                                        <source :src="lesson.video" type="video/mp4" />
                                    </video>
                                </div>

                                <div class="mt-2">
                                    <h5>Quiz</h5>
                                    <div v-for="(question, qIndex) in lesson.quiz" :key="qIndex">
                                        <h6><strong>Q{{ qIndex + 1 }}:</strong> {{ question.text }}</h6>
                                        <div class="form-check mb-2" v-for="(option, optIndex) in question.options"
                                            :key="optIndex">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input" /> {{ option }}
                                            </label>
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
import 'bootstrap';
import '@popperjs/core';

export default {
    data() {
        return {
            formation: {
                id: 1,
                nom: 'Formation en UX/UI',
                image: '/assets/img/dash/formation1.png',
                statut: 'oui',
                price: 2000,
            },
            startups: [
                {
                    id: 1,
                    logo: '/assets/img/dash/logo1.png',
                    presence: 1,
                    performances: 45,
                    note: 13
                },
                {
                    id: 2,
                    logo: '/assets/img/dash/logo2.png',
                    presence: 3,
                    performances: 20,
                    note: 17
                },
                {
                    id: 3,
                    logo: '/assets/img/dash/logo3.png',
                    presence: 5,
                    performances: 60,
                    note: 14
                },
                {
                    id: 4,
                    logo: '/assets/img/dash/logo1.png',
                    presence: 3,
                    performances: 30,
                    note: 10
                },
            ],
            modules: [
                {
                    id: 1,
                    name: 'Introduction au JavaScript',
                    lessons: [
                        {
                            id: 1,
                            title: 'Les bases',
                            video: 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
                            quiz: [
                                {
                                    text: 'Quel mot-clé pour déclarer une variable ?',
                                    options: ['var', 'let', 'const', 'define']
                                },
                                {
                                    text: 'Quelle méthode convertit une chaîne en majuscules ?',
                                    options: ['toUpper()', 'toUpperCase()', 'upper()', 'capitalize()']
                                }
                            ]
                        },
                        {
                            id: 2,
                            title: 'Les conditions',
                            video: 'videos/lesson2.mp4',
                            quiz: [
                                {
                                    text: 'Quel opérateur est utilisé pour une condition stricte ?',
                                    options: ['==', '===', '=', '!=']
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 2,
                    name: 'Introduction au JavaScript',
                    lessons: [
                        {
                            id: 1,
                            title: 'Les bases',
                            video: 'videos/lesson1.mp4',
                            quiz: [
                                {
                                    text: 'Quel mot-clé pour déclarer une variable ?',
                                    options: ['var', 'let', 'const', 'define']
                                },
                                {
                                    text: 'Quelle méthode convertit une chaîne en majuscules ?',
                                    options: ['toUpper()', 'toUpperCase()', 'upper()', 'capitalize()']
                                }
                            ]
                        },
                        {
                            id: 2,
                            title: 'Les conditions',
                            video: 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
                            quiz: [
                                {
                                    text: 'Quel opérateur est utilisé pour une condition stricte ?',
                                    options: ['==', '===', '=', '!=']
                                }
                            ]
                        }
                    ]
                },
                {
                    id: 3,
                    name: 'Introduction au JavaScript',
                    lessons: [
                        {
                            id: 1,
                            title: 'Les bases',
                            video: 'https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4',
                            quiz: [
                                {
                                    text: 'Quel mot-clé pour déclarer une variable ?',
                                    options: ['var', 'let', 'const', 'define']
                                },
                                {
                                    text: 'Quelle méthode convertit une chaîne en majuscules ?',
                                    options: ['toUpper()', 'toUpperCase()', 'upper()', 'capitalize()']
                                }
                            ]
                        },
                        {
                            id: 2,
                            title: 'Les conditions',
                            video: 'videos/lesson2.mp4',
                            quiz: [
                                {
                                    text: 'Quel opérateur est utilisé pour une condition stricte ?',
                                    options: ['==', '===', '=', '!=']
                                }
                            ]
                        }
                    ]
                }
            ]

        };
    },
    methods: {
        getNoteClass(note) {
            if (note >= 15 && note <= 20) {
                return 'bg-gray-dark';
            } else if (note >= 11 && note <= 14) {
                return 'bg-yellow';
            } else if (note >= 0 && note <= 10) {
                return 'bg-orange';
            } else {
                return ''; // au cas où la note est hors de 0-20
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
            $("#startupsTable").DataTable({
                searching: false, // Supprime la barre de recherche
                ordering: false,  // Désactive le tri
                paging: true,     // Active la pagination
                info: false,      // Masque "Showing X to Y of Z entries"
                language: {
                    paginate: {
                        previous: "", // Supprime "Précédent"
                        next: "",     // Supprime "Suivant"
                    },
                    lengthMenu: "Afficher _MENU_ entrées",
                    zeroRecords: "Aucun enregistrement trouvé",
                    infoEmpty: "",
                    infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
                },
                dom: "tp", // Supprime les boutons inutiles (t = tableau, p = pagination)
            });
        });
    }
};
</script>