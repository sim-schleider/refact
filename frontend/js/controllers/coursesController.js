import { coursesAPI } from '../api/coursesAPI.js';

document.addEventListener('DOMContentLoaded', () => 
{
    loadCourses();
    setupCourseFormHandler();
});

function setupCourseFormHandler() 
{
  const form = document.getElementById('courseForm');
  form.addEventListener('submit', async e => 
  {
        e.preventDefault();
        const course = 
        {
            id: document.getElementById('courseId').value.trim(),
            name: document.getElementById('name').value.trim()
        };

        try 
        {
            if (course.id) 
            {
                await coursesAPI.update(course);
            }
            else
            {
                await coursesAPI.create(course);
            }
            
            form.reset();
            document.getElementById('courseId').value = '';
            loadCourses();
        }
        catch (err)
        {
            console.error(err.message);
        }
  });
}

async function loadCourses()
{
    try
    {
        const courses = await coursesAPI.fetchAll();
        renderCourseTable(courses);
    }
    catch (err)
    {
        console.error('Error cargando materias:', err.message);
    }
}

function renderCourseTable(courses)
{
    const tbody = document.getElementById('courseTableBody');
    tbody.replaceChildren();

    courses.forEach(course =>
    {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(course.name));
        tr.appendChild(createCourseActionsCell(course));

        tbody.appendChild(tr);
    });
}

function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createCourseActionsCell(course)
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => 
    {
        document.getElementById('courseId').value = course.id;
        document.getElementById('name').value = course.name;
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDeleteCourse(course.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

async function confirmDeleteCourse(id)
{
    if (!confirm('¿Seguro que deseas borrar esta materia?')) return;

    try
    {
        await coursesAPI.remove(id);
        loadCourses();
    }
    catch (err)
    {
        console.error('Error al borrar materia:', err.message);
    }
}
