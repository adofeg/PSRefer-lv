export const normalizeCollection = (payload) => {
    if (Array.isArray(payload)) {
        return payload;
    }

    if (!payload || typeof payload !== 'object') {
        return [];
    }

    if (Array.isArray(payload.data)) {
        return payload.data;
    }

    if (Array.isArray(payload.items)) {
        return payload.items;
    }

    return [];
};

export const normalizePaginated = (payload) => {
    if (Array.isArray(payload)) {
        return { data: payload, links: [], meta: null };
    }

    if (!payload || typeof payload !== 'object') {
        return { data: [], links: [], meta: null };
    }

    return {
        data: normalizeCollection(payload),
        links: Array.isArray(payload.links) ? payload.links : [],
        meta: payload.meta ?? null,
    };
};

export const normalizeResource = (payload, fallback = null) => {
    if (payload === undefined || payload === null) {
        return fallback;
    }

    if (typeof payload !== 'object') {
        return payload;
    }

    if (payload.data && typeof payload.data === 'object' && !Array.isArray(payload.data)) {
        return payload.data;
    }

    return payload;
};
