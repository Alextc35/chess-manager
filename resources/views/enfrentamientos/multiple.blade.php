<form action="{{ route('enfrentamientos.generar') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Seleccionar Liga</label>
        <select name="liga" id="ligaSelect" class="form-select">
            @foreach($ligas as $liga)
                <option value="{{ $liga }}">{{ ucfirst($liga) }}</option>
            @endforeach
        </select>
    </div>

    <div id="alumnosContainer" class="mb-3">
        {{-- Aquí se cargarán los checkboxes de alumnos vía JS --}}
    </div>

    <button class="btn btn-success">Generar Sesión</button>
</form>

<script>
    const alumnosContainer = document.getElementById('alumnosContainer');
    const ligaSelect = document.getElementById('ligaSelect');

    // Datos de alumnos por liga
    const alumnosData = @json($temporada->alumnos->groupBy('liga'));

    function renderAlumnos(liga) {
        alumnosContainer.innerHTML = '';
        const alumnos = alumnosData[liga] || [];
        alumnos.forEach(a => {
            const div = document.createElement('div');
            div.className = 'form-check';
            div.innerHTML = `
                <input class="form-check-input" type="checkbox" name="alumnos[]" value="${a.id}" id="alumno${a.id}">
                <label class="form-check-label" for="alumno${a.id}">${a.nombre} ${a.apellidos}</label>
            `;
            alumnosContainer.appendChild(div);
        });
    }

    ligaSelect.addEventListener('change', e => renderAlumnos(e.target.value));

    // Cargar la liga por defecto al inicio
    renderAlumnos(ligaSelect.value);
</script>
