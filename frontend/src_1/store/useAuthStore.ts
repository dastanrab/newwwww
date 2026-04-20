import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { SettingsData } from "../hooks/useSettings.ts";
import { useSettings } from "../hooks/useSettings.ts";

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
                const { accessToken, logout, setSetting } = get();
                if (!accessToken) return;

                set({ loadingSettings: true, settingsError: null });

                try {
                    const { getSettings } = useSettings();
                    const res = await getSettings(accessToken);

                    if (res.status === "success") {
                        setSetting(res.data);
                    } else {
                        set({ settingsError: res.message });
                    }
                } catch (error: any) {
                    set({ settingsError: "خطا در بروزرسانی تنظیمات" });
                    if (error?.response?.status === 401) {
                        logout();
                        window.location.href = "/login";
                    }
                } finally {
                    set({ loadingSettings: false });
                }
            },
        }),
        { name: "auth-storage" }
    )
);