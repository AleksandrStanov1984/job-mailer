import { showToast } from '../../shared/toast';

const STORAGE_KEY = 'jobMailerSettings';

export function initSettings() {
    const duplicateProtection =
        document.getElementById('duplicate_protection');

    const duplicateDays =
        document.getElementById('duplicate_days');

    const delaySeconds =
        document.getElementById('delay_seconds');

    const saveButton =
        document.getElementById('save-settings-button');

    if (
        !duplicateProtection ||
        !duplicateDays ||
        !delaySeconds ||
        !saveButton
    ) {
        return;
    }

    loadSettings(
        duplicateProtection,
        duplicateDays,
        delaySeconds
    );

    saveButton.addEventListener('click', () => {
        const days = Number(duplicateDays.value);
        const delay = Number(delaySeconds.value);

        if (!Number.isInteger(days) || days < 1) {
            showToast(
                'Количество дней должно быть больше нуля',
                'error'
            );

            return;
        }

        if (!Number.isInteger(delay) || delay < 0) {
            showToast(
                'Пауза не может быть отрицательной',
                'error'
            );

            return;
        }

        const settings = {
            duplicateProtection: duplicateProtection.checked,
            duplicateDays: days,
            delaySeconds: delay,
        };

        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify(settings)
        );

        showToast('Настройки сохранены');
    });
}


function loadSettings(
    duplicateProtection,
    duplicateDays,
    delaySeconds
) {
    const stored =
        localStorage.getItem(STORAGE_KEY);

    if (!stored) {
        return;
    }

    try {
        const settings =
            JSON.parse(stored);

        duplicateProtection.checked =
            settings.duplicateProtection ?? true;

        duplicateDays.value =
            settings.duplicateDays ?? 7;

        delaySeconds.value =
            settings.delaySeconds ?? 5;

    } catch {
        localStorage.removeItem(STORAGE_KEY);
    }
}
