import { create } from "zustand";
import { persist } from "zustand/middleware";

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
    setting: []

    setSetting: (setting: []) =>void;
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
            setting:[],

            setSetting: (setting) => set({ setting }),
            setMob: (mob) => set({ mob }),
            setAccessToken: (token) => set({ accessToken: token }),
            setProfile: (profile) => set({ profile }),
            logout: () =>
                set({
                    accessToken: null,
                    mob: null,
                    profile: null,
                }),
        }),
        {
            name: "auth-storage",
        }
    )
);
