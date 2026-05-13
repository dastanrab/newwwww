// src/store/useDriverWastesStore.ts
import { create } from "zustand";
import { persist } from "zustand/middleware";

export type RegisteredWasteLine = {
    wasteId: number;
    title: string;
    kg: number;
    unitPrice: number;
    lineTotal: number;
    receiveId: number;
};

type DriverWastesState = {
    wastesByRequest: Record<number, RegisteredWasteLine[]>;
    setWastes: (requestId: number, wastes: RegisteredWasteLine[]) => void;
    clearWastes: (requestId: number) => void;
    getWastes: (requestId: number) => RegisteredWasteLine[];
};

export const useDriverWastesStore = create<DriverWastesState>()(
    persist(
        (set, get) => ({
            wastesByRequest: {},

            setWastes: (requestId, wastes) =>
                set((state) => ({
                    wastesByRequest: {
                        ...state.wastesByRequest,
                        [requestId]: wastes,
                    },
                })),

            clearWastes: (requestId) =>
                set((state) => {
                    const { [requestId]: _, ...rest } = state.wastesByRequest;
                    return { wastesByRequest: rest };
                }),

            getWastes: (requestId) => get().wastesByRequest[requestId] || [],
        }),
        {
            name: "driver-wastes-storage",
        }
    )
);
