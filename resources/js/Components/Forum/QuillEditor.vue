<template>
    <div class="quill-container">
        <div class="quill-editor" ref="editor"></div>
    </div>
</template>

<script>
import { onMounted, ref, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

export default {
    name: 'QuillEditor',
    props: {
        modelValue: String,
        placeholder: {
            type: String,
            default: 'Détaillez votre sujet ici...'
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const editor = ref(null);
        let quill = null;

        onMounted(() => {
            quill = new Quill(editor.value, {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'align': [] }],
                            ['link', 'image'],
                            ['clean']
                        ],
                        handlers: {
                            image: imageHandler,
                            link: linkHandler
                        }
                    }
                },
                placeholder: props.placeholder,
            });

            if (props.modelValue) {
                quill.root.innerHTML = props.modelValue;
            }

            quill.on('text-change', () => {
                emit('update:modelValue', quill.root.innerHTML);
            });
        });

        const imageHandler = () => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                const file = input.files[0];
                if (file) {
                    try {
                        const formData = new FormData();
                        formData.append('image', file);
                        
                        // Insérer un placeholder pendant le chargement
                        const range = quill.getSelection(true);
                        const placeholder = 'Chargement de l\'image...';
                        quill.insertText(range.index, placeholder, {
                            'color': '#999',
                            'italic': true
                        });
                        
                        // Envoyer l'image au serveur
                        const response = await axios.post('/upload-image', formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        });

                        // Supprimer le placeholder
                        quill.deleteText(range.index, placeholder.length);
                        
                        // Insérer l'image avec l'URL retournée
                        quill.insertEmbed(range.index, 'image', response.data.url);
                        quill.setSelection(range.index + 1);
                    } catch (error) {
                        console.error('Erreur lors du téléchargement de l\'image:', error);
                        alert('Erreur lors du téléchargement de l\'image. Veuillez réessayer.');
                    }
                }
            };
        };

        const linkHandler = () => {
            const range = quill.getSelection();
            if (range) {
                const value = prompt('Entrez l\'URL du lien:');
                if (value) {
                    quill.format('link', value);
                } else {
                    quill.format('link', false);
                }
            }
        };

        watch(() => props.modelValue, (newValue) => {
            if (quill && newValue !== quill.root.innerHTML) {
                quill.root.innerHTML = newValue;
            }
        });

        return {
            editor,
        };
    },
};
</script>

<style>
.quill-container {
    background: white;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.quill-editor {
    min-height: 200px;
    max-height: 400px;
    overflow-y: auto;
}

.ql-toolbar.ql-snow {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}

.ql-container.ql-snow {
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
    border: 1px solid #e2e8f0;
    border-top: none;
}

.ql-editor {
    font-size: 1rem;
    line-height: 1.5;
    padding: 1rem;
}

.ql-editor p {
    margin-bottom: 0.5rem;
}

.ql-editor img {
    max-width: 300px;
    max-height: 300px;
    height: auto;
    display: block;
    margin: 1em auto;
    object-fit: contain;
}

.ql-editor a {
    color: #2563eb;
    text-decoration: underline;
}

.ql-editor a:hover {
    color: #1d4ed8;
}
</style>
