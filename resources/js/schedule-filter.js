function filterSchedulesByStore() {
    const storeSelect = document.getElementById('tienda_id');
    const scheduleSelect = document.getElementById('horario_id');

    if (!storeSelect || !scheduleSelect) {
        return;
    }

    const applyFilter = () => {
        const selectedStore = storeSelect.value;

        Array.from(scheduleSelect.options).forEach((option) => {
            if (option.value === '') {
                option.hidden = false;
                return;
            }

            const matches = option.dataset.tiendaId === selectedStore;
            option.hidden = !matches;

            if (!matches && option.selected) {
                scheduleSelect.value = '';
            }
        });
    };

    storeSelect.addEventListener('change', applyFilter);
    applyFilter();
}

document.addEventListener('DOMContentLoaded', filterSchedulesByStore);