import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { SettingsData } from "../hooks/useSettings.ts";

const BASE_URL = "http://185.255.88.111:8000/api/driver";

interface UserProfile {
    id?: number;
    firstName?: string;
    lastName?: string;
    gender?: "male" | "female";
    email?: string;
    birthDate?: string;
    userType?: "citizen" | "guild";
    guildMarket?: number;
    guildTitle?: string;
}

interface AuthState {
    accessToken: string | null;
    mob: string | null;
    profile: UserProfile | null;
    setting: SettingsData | null;

    loadingSettings: boolean;
    settingsError: string | null;

    setSetting: (
        setting: SettingsData | ((prev: SettingsData | null) => SettingsData)
    ) => void;
    setMob: (mob: string) => void;
    setAccessToken: (token: string) => void;
    setProfile: (profile: UserProfile) => void;
    logout: () => void;

    refreshSettings: () => Promise<void>;
}

export const useAuthStore = create<AuthState>()(
    persist(
        (set, get) => ({
            accessToken: null,
            mob: null,
            profile: null,
            setting: null,
            loadingSettings: false,
            settingsError: null,

            setSetting: (setting) =>
                set((state) => ({
                    setting:
                        typeof setting === "function"
                            ? setting(state.setting)
                            : { ...(state.setting ?? {}), ...setting },
                })),
            setMob: (mob) => set({ mob }),
            setAccessToken: (token) => set({ accessToken: token }),
            setProfile: (profile) => set({ profile }),
            logout: () =>
                set({
                    accessToken: null,
                    mob: null,
                    profile: null,
                    setting: null,
                    loadingSettings: false,
                    settingsError: null,
                }),

            refreshSettings: async () => {
                const { accessToken, logout } = get();
                if (!accessToken) return;

                console.log('start refresh setting');
                set({ loadingSettings: true, settingsError: null });

                try {
                    const res = await fetch(`${BASE_URL}/settings`, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            Accept: "application/json",
                            Authorization: `Bearer ${accessToken}`,
                        },
                    });

                    const json = await res.json();

                    if (json.status === "success") {
                        console.log('ok get setting');
                        set({ setting: json.data });
                        console.log('set setting');
                    } else {
                        console.log('error in get setting');
                        set({ settingsError: json.message });
                    }
                } catch (error: any) {
                    console.log('error setting', error);
                    set({ settingsError: "خطا در بروزرسانی تنظیمات" });
                } finally {
                    set({ loadingSettings: false });
                }
            },
        }),
        { name: "auth-storage" }
    )
);
