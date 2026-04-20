// src/store/useUserStore.ts
import { create } from 'zustand'
import { persist, createJSONStorage } from 'zustand/middleware'

// تعریف تایپ اطلاعات کاربر
export interface UserInfo {
    id: string | null
    firstName: string | null
    lastName: string | null
    mobile: string | null
    email: string | null
    avatar: string | null
    role: 'user' | 'admin' | 'guest' | null
    createdAt: string | null
    lastLogin: string | null
}

// تعریف تایپ‌های استور کاربر
interface UserState {
    // وضعیت کاربر
    user: UserInfo
    isProfileComplete: boolean
    preferences: {
        language: 'fa' | 'en'
        theme: 'light' | 'dark' | 'system'
        notifications: boolean
    }

    // اکشن‌ها
    setUser: (userInfo: Partial<UserInfo>) => void
    updateUser: (updates: Partial<UserInfo>) => void
    clearUser: () => void
    setPreference: <K extends keyof UserState['preferences']>(
        key: K,
        value: UserState['preferences'][K]
    ) => void
    fetchUserProfile: (userId: string) => Promise<void>
}

// مقادیر پیش‌فرض کاربر
const initialUser: UserInfo = {
    id: null,
    firstName: null,
    lastName: null,
    mobile: null,
    email: null,
    avatar: null,
    role: 'guest',
    createdAt: null,
    lastLogin: null
}

// ایجاد استور کاربر با قابلیت ذخیره‌سازی
export const useUserStore = create<UserState>()(
    persist(
        (set) => ({
            // مقادیر اولیه
            user: initialUser,
            isProfileComplete: false,
            preferences: {
                language: 'fa',
                theme: 'system',
                notifications: true
            },

            // تنظیم کامل اطلاعات کاربر
            setUser: (userInfo) => {
                set((state) => {
                    const updatedUser = { ...state.user, ...userInfo }
                    const isProfileComplete = Boolean(
                        updatedUser.firstName &&
                        updatedUser.lastName &&
                        updatedUser.email &&
                        updatedUser.mobile
                    )

                    return {
                        user: updatedUser,
                        isProfileComplete
                    }
                })
            },

            // به‌روزرسانی جزئی اطلاعات کاربر
            updateUser: (updates) => {
                set((state) => {
                    const updatedUser = { ...state.user, ...updates }
                    const isProfileComplete = Boolean(
                        updatedUser.firstName &&
                        updatedUser.lastName &&
                        updatedUser.email &&
                        updatedUser.mobile
                    )

                    return {
                        user: updatedUser,
                        isProfileComplete
                    }
                })
            },

            // پاک کردن اطلاعات کاربر
            clearUser: () => set({
                user: initialUser,
                isProfileComplete: false
            }),

            // تنظیم ترجیحات کاربر
            setPreference: (key, value) => {
                set((state) => ({
                    preferences: {
                        ...state.preferences,
                        [key]: value
                    }
                }))
            },

            // دریافت پروفایل از سرور
            fetchUserProfile: async (userId) => {
                try {
                    // اینجا می‌توانید از API واقعی استفاده کنید
                    // const response = await api.get(`/users/${userId}`)

                    // شبیه‌سازی دریافت اطلاعات
                    const mockResponse = {
                        id: userId,
                        firstName: 'علی',
                        lastName: 'محمدی',
                        mobile: '09123456789',
                        email: 'ali@example.com',
                        avatar: null,
                        role: 'user' as const,
                        createdAt: new Date().toISOString(),
                        lastLogin: new Date().toISOString()
                    }

                    set((state) => ({
                        user: { ...state.user, ...mockResponse },
                        isProfileComplete: true
                    }))
                } catch (error) {
                    console.error('خطا در دریافت پروفایل:', error)
                }
            }
        }),
        {
            name: 'user-storage',
            storage: createJSONStorage(() => localStorage),
            partialize: (state) => ({
                user: state.user,
                preferences: state.preferences
            })
        }
    )
)