import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { SettingsData } from "../hooks/useSettings.ts";

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

    setSetting: (setting: SettingsData) => void;
    setMob: (mob: string) => void;
    setAccessToken: (token: string) => void;
    setProfile: (profile: UserProfile) => void;
    logout: () => void;
}
export const useAuthStore = create<AuthState>()(
    persist(
        (set) => ({
            accessToken: null,
            mob: null,
            profile: null,
            setting: null,

            setSetting: (setting) => set({ setting }),
            setMob: (mob) => set({ mob }),
            setAccessToken: (token) => set({ accessToken: token }),
            setProfile: (profile) => set({ profile }),
            logout: () =>
                set({
                    accessToken: null,
                    mob: null,
                    profile: null,
                    setting: null,
                }),
        }),
        {
            name: "auth-storage",
        }
    )
);
