<template>
    

    <!-- Liste des modules -->
    <div v-for="(module, moduleIndex) in modules" :key="moduleIndex" class="mb-3">
        <div class="accordion accordionModule" :id="'accordionModule' + moduleIndex">
            <div class="card accordion-item">
                <div class="" :id="'headingModule' + moduleIndex">
                    <h2 class="mb-0">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            :data-bs-target="'#collapseModule' + moduleIndex" aria-expanded="true"
                            :aria-controls="'collapseModule' + moduleIndex">
                            <div class="d-flex justify-content-between w-100">
                                {{ module.title }}
                                <button class="btn btn-rect primary me-2" @click="removeModule(moduleIndex)">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </button>
                    </h2>
                </div>

                <div :id="'collapseModule' + moduleIndex" class="collapse show"
                    :aria-labelledby="'headingModule' + moduleIndex" :data-bs-parent="'#accordionModule' + moduleIndex">
                    <div class="accordion-body pt-4">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label for="" class="form-label">Description du module</label>
                                <div class="form-control">
                                    {{ module.description }}
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 mb-4">
                                <label for="" class="form-label">Ordre*</label>
                                <div class="form-control">
                                    <p>{{ module.order }}</p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-12 mb-4">
                                <label for="" class="form-label">Durée*</label>
                                <div class="form-control">
                                    <p>{{ module.duration }} semaines</p>
                                </div>
                            </div>
                        </div>
                        <!--Lecon-->
                        <div class="box-blue">
                            <!-- Liste des leçons -->
                            <div v-for="(lecon, leconIndex) in module.lecons" :key="leconIndex" class="mb-3">
                                <div class="accordion accordionLecon" :id="'accordionLecon' + moduleIndex + '-' + leconIndex">
                                    <div class="card accordion-item">
                                        <div class="" :id="'headingLecon' + moduleIndex + '-' + leconIndex">
                                            <h2 class="mb-0">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    :data-bs-target="'#collapseLecon' + moduleIndex + '-' + leconIndex" aria-expanded="true"
                                                    :aria-controls="'collapseLecon' + moduleIndex + '-' + leconIndex">
                                                    <div class="d-flex justify-content-between w-100">
                                                        {{ lecon.title || 'Nouvelle leçon' }}
                                                        <button class="btn btn-rect primary me-2"
                                                            @click="removeLecon(moduleIndex, leconIndex)">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </button>
                                            </h2>
                                        </div>

                                        <div :id="'collapseLecon' + moduleIndex + '-' + leconIndex" class="collapse show"
                                            :aria-labelledby="'headingLecon' + moduleIndex + '-' + leconIndex"
                                            :data-bs-parent="'#accordionLecon' + moduleIndex + '-' + leconIndex">
                                            <div class="accordion-body pt-4">
                                                <div class="nav-align-top mx-0 mb-4">
                                                    <ul class="nav nav-tabs mx-0" role="tablist">
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link active" role="tab"
                                                                data-bs-toggle="tab" :data-bs-target="'#navs-top-contenu-' + moduleIndex + '-' + leconIndex"
                                                                aria-controls="navs-top-contenu"
                                                                aria-selected="true">Contenu</button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link" role="tab"
                                                                data-bs-toggle="tab"
                                                                :data-bs-target="'#navs-top-documents-' + moduleIndex + '-' + leconIndex"
                                                                aria-controls="navs-top-documents"
                                                                aria-selected="false">Vidéo et documents</button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link" role="tab"
                                                                data-bs-toggle="tab" :data-bs-target="'#navs-top-quiz-' + moduleIndex + '-' + leconIndex"
                                                                aria-controls="navs-top-quiz"
                                                                aria-selected="false">Quiz</button>
                                                        </li>
                                                    </ul>
                                                    <div class="tab-content">
                                                        <!--CONTENU-->
                                                        <div class="tab-pane fade show active" :id="'navs-top-contenu-' + moduleIndex + '-' + leconIndex"
                                                            role="tabpanel">
                                                            <div class="row">
                                                                <div class="col-12 mb-4">
                                                                    <label for="" class="form-label">Titre du leçon*</label>
                                                                    <input type="text" class="form-control" v-model="lecon.title"
                                                                        placeholder="Titre Leçon">
                                                                </div>
                                                                <div class="col-12 mb-4">
                                                                    <label for="" class="form-label">Contenu textuelle du leçon*</label>
                                                                    <textarea name="" id="" rows="4"
                                                                        class="form-control" v-model="lecon.content"
                                                                        placeholder="Contenu détaillé de la leçon..."></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--VIDEO - DOCUMENTS-->
                                                        <div class="tab-pane fade" :id="'navs-top-documents-' + moduleIndex + '-' + leconIndex"
                                                            role="tabpanel">
                                                            <div class="row">
                                                                <div class="col-12 mb-4">
                                                                    <label for="" class="form-label">Ajouter une vidéo</label>
                                                                    <!-- Liste des vidéos ajoutées -->
                                                                    <div v-if="lecon.videos && lecon.videos.length > 0" class="videos-list">
                                                                        <div v-for="(video, videoIndex) in lecon.videos" :key="videoIndex" class="video-item mb-3">
                                                                            <div class="preview w-100">
                                                                                <div class="video-preview w-100">
                                                                                    <video v-if="video.type === 'file'" :src="video.url" controls class="w-100"></video>
                                                                                    <div v-else class="video-placeholder">
                                                                                        <i class="ti ti-video"></i>
                                                                                        <p class="my-0">{{ video.name }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <button class="btn btn-rect danger" @click="removeVideo(moduleIndex, leconIndex, videoIndex)">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Box d'ajout de vidéo -->
                                                                    <div class="addBox p-lg-3 p-2 mb-4">
                                                                        <input type="file" :id="'video-' + moduleIndex + '-' + leconIndex"
                                                                            class="form-control d-none" accept="video/*" @change="handleVideoUpload($event, moduleIndex, leconIndex)">
                                                                        <label :for="'video-' + moduleIndex + '-' + leconIndex"
                                                                            class="btn btn-primary mb-3 fs-sm me-2">Séléctionner
                                                                            un fichier</label>
                                                                        <button class="btn btn-primary mb-3 fs-sm me-2"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#importVideoModal">
                                                                            Sélectionner depuis la bibliothèque</button>
                                                                        <p>Aucun fichier choisi</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 mb-4">
                                                                    <label for="" class="form-label">Ajouter un document</label>
                                                                    <!-- Liste des documents ajoutés -->
                                                                    <div v-if="lecon.documents && lecon.documents.length > 0" class="documents-list">
                                                                        <div v-for="(document, docIndex) in lecon.documents" :key="docIndex" class="document-item mb-3">
                                                                            <div class="preview">
                                                                                <div class="document-preview">
                                                                                    <i class="ti ti-file"></i>
                                                                                    <p class="my-0">{{ document.name }}</p>
                                                                                </div>
                                                                                <button class="btn btn-rect danger" @click="removeDocument(moduleIndex, leconIndex, docIndex)">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Box d'ajout de document -->
                                                                    <div class="addBox p-lg-3 p-2 mb-4">
                                                                        <input type="file" :id="'file-' + moduleIndex + '-' + leconIndex"
                                                                            class="form-control d-none" accept=".pdf,.doc,.docx" @change="handleDocumentUpload($event, moduleIndex, leconIndex)">
                                                                        <label :for="'file-' + moduleIndex + '-' + leconIndex"
                                                                            class="btn btn-primary mb-3 fs-sm me-2">Séléctionner
                                                                            un fichier</label>
                                                                        <button class="btn btn-primary mb-3 fs-sm me-2">Sélectionner
                                                                            depuis la bibliothèque</button>
                                                                        <p>Aucun fichier choisi</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--QUIZ-->
                                                        <div class="tab-pane fade" :id="'navs-top-quiz-' + moduleIndex + '-' + leconIndex" role="tabpanel">
                                                            <div class="row quiz">
                                                                <div class="col-12 mb-4">
                                                                    <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                                                                        <label for="" class="form-label">Titre du Quiz*</label>
                                                                        <button class="btn btn-rect danger" @click="removeQuiz(moduleIndex, leconIndex)">
                                                                            <i class="ti ti-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                    <input type="text" class="form-control" v-model="lecon.quiz.title"
                                                                        placeholder="Entrer un titre">
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-12 mb-4">
                                                                    <label for="" class="form-label">Note final*</label>
                                                                    <input type="number" class="form-control" v-model="lecon.quiz.finalNote"
                                                                        placeholder="Entrer une note">
                                                                </div>
                                                                <div class="col-lg-6 col-md-6 col-12 mb-4">
                                                                    <label for="" class="form-label">Durée du Quiz*</label>
                                                                    <input type="number" class="form-control" v-model="lecon.quiz.duration"
                                                                        placeholder="Entrer une durée">
                                                                </div>
                                                            </div>
                                                            <div class="accordion accordionQuestion mt-3">
                                                                <div v-for="(question, questionIndex) in lecon.quiz.questions" :key="questionIndex" class="card accordion-item mb-4">
                                                                    <div class="" :id="'headingQuestion-' + moduleIndex + '-' + leconIndex + '-' + questionIndex">
                                                                        <h2 class="mb-0">
                                                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                                                :data-bs-target="'#collapseQuestion-' + moduleIndex + '-' + leconIndex + '-' + questionIndex"
                                                                                aria-expanded="true"
                                                                                :aria-controls="'collapseQuestion-' + moduleIndex + '-' + leconIndex + '-' + questionIndex">
                                                                                <div class="d-flex justify-content-between w-100">
                                                                                    Question {{ questionIndex + 1 }}
                                                                                    <button class="btn btn-rect danger me-2"
                                                                                        @click="removeQuestion(moduleIndex, leconIndex, questionIndex)">
                                                                                        <i class="ti ti-trash"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </button>
                                                                        </h2>
                                                                    </div>
                                                                    <div :id="'collapseQuestion-' + moduleIndex + '-' + leconIndex + '-' + questionIndex"
                                                                        class="collapse show"
                                                                        :aria-labelledby="'headingQuestion-' + moduleIndex + '-' + leconIndex + '-' + questionIndex">
                                                                        <div class="accordion-body pt-4">
                                                                            <div class="row">
                                                                                <div class="col-12 mb-4">
                                                                                    <input type="text"
                                                                                        class="form-control mb-4" v-model="question.text"
                                                                                        placeholder="Ecrire le texte du question içi...">
                                                                                    <div v-for="(answer, answerIndex) in question.answers" :key="answerIndex"
                                                                                        class="reponse p-lg-3 p-md-2 p-2 mb-4">
                                                                                        <div class="form-check w-100 me-2">
                                                                                            <input type="checkbox"
                                                                                                class="form-check-input" v-model="answer.isCorrect">
                                                                                            <input type="text"
                                                                                                class="input-check-text" v-model="answer.text"
                                                                                                placeholder="Ecrire la réponse içi...">
                                                                                        </div>
                                                                                        <button class="btn btn-rect danger"
                                                                                            @click="removeAnswer(moduleIndex, leconIndex, questionIndex, answerIndex)">
                                                                                            <i class="ti ti-trash"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                    <button class="btn btn-secondary fs-sm bg-transp"
                                                                                        @click="addAnswer(moduleIndex, leconIndex, questionIndex)">Ajouter
                                                                                        une réponse</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mt-4 mb-4">
                                                                <button class="btn btn-secondary fs-sm bg-transp"
                                                                    @click="addQuestion(moduleIndex, leconIndex)">Ajouter
                                                                    une question</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mt-3 mb-3">
                                <button class="btn btn-secondary fs-sm" @click="addLecon(moduleIndex)">Ajouter une leçon</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire du module -->
    <div class="row module mb-3">
        <div class="card card-1 mb-4" style="height: auto;">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-4">
                        <p class="text-orange fw-bold">*Représente un champ obligatoire</p>
                    </div>
                    <div class="col-12 mb-4">
                        <label for="" class="form-label">Titre du module*</label>
                        <input type="text" class="form-control" v-model="newModule.title" placeholder="Titre module 1" />
                    </div>
                    <div class="col-12 mb-4">
                        <label for="" class="form-label">Description du module</label>
                        <textarea rows="4" class="form-control" v-model="newModule.description" placeholder="Ecrire içi..."></textarea>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12 mb-4">
                        <label for="" class="form-label">Ordre*</label>
                        <input type="number" class="form-control" v-model="newModule.order" placeholder="Entrer un nombre" />
                    </div>

                    <div class="col-lg-6 col-md-6 col-12 mb-4">
                        <label for="" class="form-label">Durée*</label>
                        <input type="number" class="form-control" v-model="newModule.duration" placeholder="Entrer une durée" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 mt-3 mb-3">
        <button class="btn btn-primary fs-sm" @click="addModule">Ajouter un module</button>
    </div>

    <!--import video  Modal -->
    <div class="modal fade modal-1" id="importVideoModal" tabindex="-1" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body pt-5">
                    <div class="formulaire">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="mb-3 px-lg-4 px-md-2 px-0">
                                    <div class="jointe border-0 shadow-none text-center">
                                        <div class="box">
                                            <img src="/assets/img/dash/doc.png" alt="">
                                        </div>
                                        <input type="file" id="file" class="d-none" @change="updateFileName">
                                        <p>{{ fileName }}</p>
                                        <small>Sélectionner un document, un vidéo, une image ou un lien depuis la
                                            bibliothèque</small>
                                        <label for="file" class=" btn fw-bold mt-3">Sélectionner un fichier</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 btnsBox d-lg-flex d-md-flex d-sm-flex d-block justify-content-center">
                                <button class="btn btn-primary fs-sm me-2" @click="addFromLibrary">Ajouter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            fileName: '',
            selectedFile: null,
            newModule: {
                title: '',
                description: '',
                order: '',
                duration: '',
                lecons: []
            },
            modules: []
        };
    },
    methods: {
        addModule() {
            if (this.newModule.title && this.newModule.order && this.newModule.duration) {
                this.modules.push({
                    ...this.newModule,
                    lecons: [{
                        title: '',
                        content: '',
                        quiz: {
                            title: '',
                            finalNote: '',
                            duration: '',
                            questions: [{
                                text: '',
                                answers: [{
                                    text: '',
                                    isCorrect: false
                                }]
                            }]
                        }
                    }]
                });
                this.newModule = {
                    title: '',
                    description: '',
                    order: '',
                    duration: '',
                    lecons: []
                };
            }
        },
        removeModule(index) {
            this.modules.splice(index, 1);
        },
        addLecon(moduleIndex) {
            this.modules[moduleIndex].lecons.push({
                title: '',
                content: '',
                quiz: {
                    title: '',
                    finalNote: '',
                    duration: '',
                    questions: [{
                        text: '',
                        answers: [{
                            text: '',
                            isCorrect: false
                        }]
                    }]
                }
            });
        },
        removeLecon(moduleIndex, leconIndex) {
            this.modules[moduleIndex].lecons.splice(leconIndex, 1);
        },
        addQuestion(moduleIndex, leconIndex) {
            this.modules[moduleIndex].lecons[leconIndex].quiz.questions.push({
                text: '',
                answers: [{
                    text: '',
                    isCorrect: false
                }]
            });
        },
        removeQuestion(moduleIndex, leconIndex, questionIndex) {
            this.modules[moduleIndex].lecons[leconIndex].quiz.questions.splice(questionIndex, 1);
        },
        addAnswer(moduleIndex, leconIndex, questionIndex) {
            this.modules[moduleIndex].lecons[leconIndex].quiz.questions[questionIndex].answers.push({
                text: '',
                isCorrect: false
            });
        },
        removeAnswer(moduleIndex, leconIndex, questionIndex, answerIndex) {
            this.modules[moduleIndex].lecons[leconIndex].quiz.questions[questionIndex].answers.splice(answerIndex, 1);
        },
        removeQuiz(moduleIndex, leconIndex) {
            this.modules[moduleIndex].lecons[leconIndex].quiz = {
                title: '',
                finalNote: '',
                duration: '',
                questions: [{
                    text: '',
                    answers: [{
                        text: '',
                        isCorrect: false
                    }]
                }]
            };
        },
        updateFileName(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.selectedFile = file;
            } else {
                this.fileName = 'Aucun fichier choisi';
                this.selectedFile = null;
            }
        },
        addFromLibrary() {
            if (this.selectedFile) {
                const file = this.selectedFile;
                const moduleIndex = this.modules.length - 1; // Dernier module
                const leconIndex = this.modules[moduleIndex].lecons.length - 1; // Dernière leçon

                if (file.type.startsWith('video/')) {
                    if (!this.modules[moduleIndex].lecons[leconIndex].videos) {
                        this.modules[moduleIndex].lecons[leconIndex].videos = [];
                    }
                    
                    const videoUrl = URL.createObjectURL(file);
                    this.modules[moduleIndex].lecons[leconIndex].videos.push({
                        name: file.name,
                        type: 'file',
                        url: videoUrl,
                        file: file
                    });
                } else {
                    if (!this.modules[moduleIndex].lecons[leconIndex].documents) {
                        this.modules[moduleIndex].lecons[leconIndex].documents = [];
                    }
                    
                    this.modules[moduleIndex].lecons[leconIndex].documents.push({
                        name: file.name,
                        type: 'file',
                        file: file
                    });
                }

                // Réinitialiser et fermer le modal
                this.selectedFile = null;
                this.fileName = '';
                
                // Fermer le modal en utilisant l'attribut data-bs-dismiss
                const modal = document.getElementById('importVideoModal');
                const closeButton = modal.querySelector('[data-bs-dismiss="modal"]');
                if (closeButton) {
                    closeButton.click();
                }
            }
        },
        handleVideoUpload(event, moduleIndex, leconIndex) {
            const file = event.target.files[0];
            if (file) {
                if (!this.modules[moduleIndex].lecons[leconIndex].videos) {
                    this.modules[moduleIndex].lecons[leconIndex].videos = [];
                }
                
                const videoUrl = URL.createObjectURL(file);
                this.modules[moduleIndex].lecons[leconIndex].videos.push({
                    name: file.name,
                    type: 'file',
                    url: videoUrl,
                    file: file
                });
                
                // Réinitialiser l'input file pour permettre d'ajouter une autre vidéo
                event.target.value = '';
            }
        },
        handleDocumentUpload(event, moduleIndex, leconIndex) {
            const file = event.target.files[0];
            if (file) {
                if (!this.modules[moduleIndex].lecons[leconIndex].documents) {
                    this.modules[moduleIndex].lecons[leconIndex].documents = [];
                }
                
                this.modules[moduleIndex].lecons[leconIndex].documents.push({
                    name: file.name,
                    type: 'file',
                    file: file
                });
                
                // Réinitialiser l'input file pour permettre d'ajouter un autre document
                event.target.value = '';
            }
        },
        removeVideo(moduleIndex, leconIndex, videoIndex) {
            const video = this.modules[moduleIndex].lecons[leconIndex].videos[videoIndex];
            if (video.url) {
                URL.revokeObjectURL(video.url);
            }
            this.modules[moduleIndex].lecons[leconIndex].videos.splice(videoIndex, 1);
        },
        removeDocument(moduleIndex, leconIndex, docIndex) {
            this.modules[moduleIndex].lecons[leconIndex].documents.splice(docIndex, 1);
        }
    }
};
</script>

<style scoped>

</style>
