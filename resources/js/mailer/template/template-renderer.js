export function renderTemplate(template, recipient) {
    if (!template) {
        return '';
    }

    const variables = buildVariables(recipient);

    return Object.entries(variables).reduce(
        (result, [key, value]) => {
            const pattern = new RegExp(
                `{{\\s*${escapeRegExp(key)}\\s*}}`,
                'g'
            );

            return result.replace(pattern, value ?? '');
        },
        template
    );
}


export function buildGreeting(recipient) {
    const contactName =
        cleanValue(recipient.contact_name);

    const salutation =
        cleanValue(recipient.contact_salutation);

    if (!contactName) {
        return 'Sehr geehrte Damen und Herren,';
    }

    const lastName = extractLastName(contactName);

    if (salutation === 'Frau') {
        return `Sehr geehrte Frau ${lastName},`;
    }

    if (salutation === 'Herr') {
        return `Sehr geehrter Herr ${lastName},`;
    }

    /*
     * Если контакт есть, но Frau/Herr в JSON не указано,
     * пол по имени не угадываем.
     */
    return `Guten Tag ${contactName},`;
}


function buildVariables(recipient) {
    return {
        company: cleanValue(recipient.company),
        email: cleanValue(recipient.email),
        vacancy: cleanValue(recipient.vacancy),
        contact_name: cleanValue(recipient.contact_name),
        contact_salutation: cleanValue(
            recipient.contact_salutation
        ),
        greeting: buildGreeting(recipient),
    };
}


function cleanValue(value) {
    if (value === null || value === undefined) {
        return '';
    }

    return String(value).trim();
}


function extractLastName(fullName) {
    const parts = fullName
        .trim()
        .split(/\s+/);

    return parts.at(-1) ?? fullName;
}


function escapeRegExp(value) {
    return value.replace(
        /[.*+?^${}()|[\]\\]/g,
        '\\$&'
    );
}
