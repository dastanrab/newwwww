import {
    createContext,
    useCallback,
    useContext,
    useMemo,
    useState,
    type ReactNode,
} from "react";

const STORAGE_KEY = "driverPhone";
const PENDING_KEY = "driverPendingPhone";

export type DriverSession = {
    phone: string;
};

type DriverSessionContextValue = {
    session: DriverSession | null;
    pendingPhone: string | null;
    login: (phone: string) => void;
    logout: () => void;
    setPendingPhoneForOtp: (phone: string) => void;
    clearPendingPhone: () => void;
};

const DriverSessionContext = createContext<DriverSessionContextValue | null>(
    null
);

function readStoredPhone(): string | null {
    try {
        return sessionStorage.getItem(STORAGE_KEY);
    } catch {
        return null;
    }
}

function readStoredPendingPhone(): string | null {
    try {
        return sessionStorage.getItem(PENDING_KEY);
    } catch {
        return null;
    }
}

export function DriverSessionProvider({ children }: { children: ReactNode }) {
    const [session, setSession] = useState<DriverSession | null>(() => {
        const phone = readStoredPhone();
        return phone ? { phone } : null;
    });
    const [pendingPhone, setPendingPhone] = useState<string | null>(() =>
        readStoredPendingPhone()
    );

    const clearPendingPhone = useCallback(() => {
        try {
            sessionStorage.removeItem(PENDING_KEY);
        } catch {
            /* ignore */
        }
        setPendingPhone(null);
    }, []);

    const setPendingPhoneForOtp = useCallback((phone: string) => {
        try {
            sessionStorage.setItem(PENDING_KEY, phone);
        } catch {
            /* ignore */
        }
        setPendingPhone(phone);
    }, []);

    const login = useCallback((phone: string) => {
        try {
            sessionStorage.removeItem(PENDING_KEY);
        } catch {
            /* ignore */
        }
        setPendingPhone(null);
        try {
            sessionStorage.setItem(STORAGE_KEY, phone);
        } catch {
            /* ignore */
        }
        setSession({ phone });
    }, []);

    const logout = useCallback(() => {
        try {
            sessionStorage.removeItem(STORAGE_KEY);
            sessionStorage.removeItem(PENDING_KEY);
        } catch {
            /* ignore */
        }
        setPendingPhone(null);
        setSession(null);
    }, []);

    const value = useMemo(
        () => ({
            session,
            pendingPhone,
            login,
            logout,
            setPendingPhoneForOtp,
            clearPendingPhone,
        }),
        [session, pendingPhone, login, logout, setPendingPhoneForOtp, clearPendingPhone]
    );

    return (
        <DriverSessionContext.Provider value={value}>
            {children}
        </DriverSessionContext.Provider>
    );
}

export function useDriverSession() {
    const ctx = useContext(DriverSessionContext);
    if (!ctx) {
        throw new Error("useDriverSession must be used within DriverSessionProvider");
    }
    return ctx;
}
