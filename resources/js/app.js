import { initFileInputs } from './mailer/files/files';
import { initSettings } from './mailer/settings/settings';

import { initTableFilter } from './mailer/recipients/table-filter';
import { initRecipients } from './mailer/recipients/recipients';

import { initPreview } from './mailer/preview/preview';

import { initTestMail } from './mailer/sending/test-mail';

import { initCampaignMail } from './mailer/sending/campaign-mail';


document.addEventListener('DOMContentLoaded', () => {
    initFileInputs();
    initSettings();

    initTableFilter();
    initRecipients();

    initPreview();

    initTestMail();
    initCampaignMail();
});
