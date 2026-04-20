import { useEffect, useState } from "react";
import {useAuthStore} from "../store/useAuthStore.ts";
import {useSettings} from "./useSettings.ts";

export const useInitApp = () => {
    const { accessToken, setSetting, logout, setting } = useAuthStore();
    const { getSettings } = useSettings();

    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const init = async () => {
            if (!accessToken) {
                setLoading(false);
                return;
            }

            // اگر قبلاً لود شده → دوباره نزن
            // @ts-ignore
            if (setting && setting.length > 0) {
                setLoading(false);
                return;
            }

            try {
                const res = await getSettings(accessToken);
                const setting=res.data
                setSetting(setting);
            } catch (error: any) {
                if (error?.response?.status === 401) {
                    logout();
                    window.location.href = "/login";
                }
            } finally {
                setLoading(false);
            }
        };

        init();
    }, [accessToken]);

    return { loading };
};