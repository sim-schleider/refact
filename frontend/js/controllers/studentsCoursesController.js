// controla el estado y aspecto de la página - módulo estudiantes-materias

// importa todas las APIs
import { studentsAPI } from '../api/studentsAPI.js';
import { coursesAPI } from '../api/coursesAPI.js';
import { studentsCoursesAPI } from '../api/studentsCoursesAPI.js';

let selectsReady = false; // previene que se pueda editar una inscripción sin que hayan cargado todas las opciones 

document.addEventListener('DOMContentLoaded', () => {
    initSelects(); // la opción alternativa al problema de 'selectsReady' es aprovechar la asincronicidad de esta función, pero esto demora la carga de la tabla, lo cual puede no ser necesario
    setupFormHandler();
    setupCancelHandler();
    loadRelations();
});

async function initSelects() {
    try {
        const students = await studentsAPI.fetchAll();
        const studentSelect = document.getElementById('studentIdSelect'); // obtiene el 'select' de estudiantes del formulario
        students.forEach(s => { // por cada estudiante (obtenido a través de la API)
            const option = document.createElement('option'); // crea una opción
            option.value = s.id; // ingresa la id (oculta)
            option.textContent = s.fullname; // ingresa el nombre
            studentSelect.appendChild(option); // agrega las opciones al select
        });

        const courses = await coursesAPI.fetchAll();
        const courseSelect = document.getElementById('courseIdSelect'); // obtiene el 'select' de materias del formulario
        courses.forEach(c => {
            const option = document.createElement('option');
            option.value = c.id;
            option.textContent = c.name;
            courseSelect.appendChild(option);
        });

        selectsReady = true; // una vez que todas las opciones de los select hayan cargado, permite la edición de inscripciones
    } catch (err) {
        console.error('Error cargando estudiantes o materias:', err.message);
    }
}

function setupFormHandler() {
    const form = document.getElementById('relationForm');
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const relation = getFormData();

        try {
            if (relation.id) {
                await studentsCoursesAPI.update(relation);
            } else {
                await studentsCoursesAPI.create(relation);
            }
            clearForm();
            loadRelations();
        } catch (err) {
            let alertmsg = '';

            switch (err.message) {
                case '1062':
                    alertmsg = 'El estudiante ya está inscrito en la materia';
                    break;
                default:
                    alertmsg = 'Error en ' + (relation.id ? 'UPDATE' : 'CREATE');
            }
            
            console.error('Error guardando inscripción:', alertmsg.toLowerCase());
            addAlert(alertmsg);
        }
    });
}

function setupCancelHandler() {
    const cancelBtn = document.getElementById('cancelBtn');
    cancelBtn.addEventListener('click', () => {
        document.getElementById('relationId').value = '';
    });
}

function getFormData() {
    return {
        id: document.getElementById('relationId').value.trim(),
        student_id: document.getElementById('studentIdSelect').value,
        course_id: document.getElementById('courseIdSelect').value,
        passed: document.getElementById('passed').checked ? 1 : 0
    };
}

function clearForm() {
    document.getElementById('relationForm').reset();
    document.getElementById('relationId').value = '';
}

async function loadRelations() {
    try {
        const relations = await studentsCoursesAPI.fetchAll(); // obtiene todas las inscripciones - esto se encarga de relacionar las dos tablas correctamente en el backend

        relations.forEach(rel => {
            rel.passed = Number(rel.passed); // convierte la condición de aprobación a un número - esto es necesario para que el string '0' sea considerado falso
        });
        
        renderRelationsTable(relations);
    } catch (err) {
        console.error('Error cargando inscripciones:', err.message);
    }
}

function renderRelationsTable(relations) {
    const tbody = document.getElementById('relationTableBody');
    tbody.replaceChildren();
    emptyAlert();

    relations.forEach(rel => {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(rel.student_fullname));
        tr.appendChild(createCell(rel.course_name));
        tr.appendChild(createCell(rel.passed ? 'Sí' : 'No')); // esto funciona gracias a haber convertido la condición a un número
        tr.appendChild(createActionsCell(rel));

        tbody.appendChild(tr);
    });
}

function createCell(text) {
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createActionsCell(relation) {
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.addEventListener('click', () => {
        if (selectsReady) // revisa que solo llene el formulario si las opciones están disponibles
            fillForm(relation);
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.addEventListener('click', () => confirmDelete(relation.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

function fillForm(relation) {
    document.getElementById('relationId').value = relation.id;
    document.getElementById('studentIdSelect').value = relation.student_id;
    document.getElementById('courseIdSelect').value = relation.course_id;
    document.getElementById('passed').checked = !!relation.passed;
}

async function confirmDelete(id) {
    if (!confirm('¿Estás seguro que deseas borrar esta inscripción?')) return;

    try {
        await studentsCoursesAPI.remove(id);
        loadRelations();
    } catch (err) {
        let alertmsg;

        switch (err.message) {
            default:
                alertmsg = 'Error en DELETE';
        }

        console.error('Error al borrar inscripción:', alertmsg.toLowerCase());
        addAlert(alertmsg);
    }
}

function addAlert(message) {
    const alertbox = document.getElementById('alertbox');
    const alert = document.createElement('p');
    alert.textContent = message;
    alertbox.appendChild(alert);

    alert.addEventListener('click', () => alert.remove());
}

function emptyAlert() {
    const alertbox = document.getElementById('alertbox');
    alertbox.replaceChildren();
}