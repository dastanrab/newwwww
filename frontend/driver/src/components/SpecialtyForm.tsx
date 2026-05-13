// src/components/SpecialtyForm.tsx
import React, { useState } from 'react';
import { SpecialtyFormConfig, FormField } from '../types/specialtyForms';

interface SpecialtyFormProps {
    config: SpecialtyFormConfig;
    onSubmit: (answers: Record<string, string>) => void;
    onSkip: () => void;
}

const SpecialtyForm: React.FC<SpecialtyFormProps> = ({ config, onSubmit, onSkip }) => {
    const [answers, setAnswers] = useState<Record<string, string>>({});
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleChange = (fieldId: string, value: string) => {
        setAnswers(prev => ({ ...prev, [fieldId]: value }));
        // پاک کردن خطا هنگام تغییر
        if (errors[fieldId]) {
            setErrors(prev => {
                const newErrors = { ...prev };
                delete newErrors[fieldId];
                return newErrors;
            });
        }
    };

    const handleMultiSelectChange = (fieldId: string, option: string) => {
        const currentValues = answers[fieldId] ? answers[fieldId].split(',') : [];
        const newValues = currentValues.includes(option)
            ? currentValues.filter(v => v !== option)
            : [...currentValues, option];

        setAnswers(prev => ({ ...prev, [fieldId]: newValues.join(',') }));
    };

    const validateForm = (): boolean => {
        const newErrors: Record<string, string> = {};

        config.fields.forEach(field => {
            if (field.required && !answers[field.id]) {
                newErrors[field.id] = 'این فیلد الزامی است';
            }
        });

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (validateForm()) {
            onSubmit(answers);
        }
    };

    const renderField = (field: FormField) => {
        const value = answers[field.id] || '';
        const error = errors[field.id];

        switch (field.type) {
            case 'text':
                return (
                    <div key={field.id} className="mb-6">
                        <label className="block text-gray-700 font-medium mb-2 text-right">
                            {field.label}
                            {field.required && <span className="text-red-500 mr-1">*</span>}
                        </label>
                        <input
                            type="text"
                            value={value}
                            onChange={(e) => handleChange(field.id, e.target.value)}
                            placeholder={field.placeholder}
                            className={`w-full px-4 py-3 border rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                error ? 'border-red-500' : 'border-gray-300'
                            }`}
                        />
                        {error && <p className="text-red-500 text-sm mt-1 text-right">{error}</p>}
                    </div>
                );

            case 'number':
                return (
                    <div key={field.id} className="mb-6">
                        <label className="block text-gray-700 font-medium mb-2 text-right">
                            {field.label}
                            {field.required && <span className="text-red-500 mr-1">*</span>}
                        </label>
                        <input
                            type="number"
                            value={value}
                            onChange={(e) => handleChange(field.id, e.target.value)}
                            placeholder={field.placeholder}
                            min={field.min}
                            max={field.max}
                            className={`w-full px-4 py-3 border rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                error ? 'border-red-500' : 'border-gray-300'
                            }`}
                        />
                        {error && <p className="text-red-500 text-sm mt-1 text-right">{error}</p>}
                    </div>
                );

            case 'select':
                return (
                    <div key={field.id} className="mb-6">
                        <label className="block text-gray-700 font-medium mb-2 text-right">
                            {field.label}
                            {field.required && <span className="text-red-500 mr-1">*</span>}
                        </label>
                        <select
                            value={value}
                            onChange={(e) => handleChange(field.id, e.target.value)}
                            className={`w-full px-4 py-3 border rounded-lg text-right focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                error ? 'border-red-500' : 'border-gray-300'
                            }`}
                        >
                            <option value="">انتخاب کنید</option>
                            {field.options?.map(option => (
                                <option key={option} value={option}>
                                    {option}
                                </option>
                            ))}
                        </select>
                        {error && <p className="text-red-500 text-sm mt-1 text-right">{error}</p>}
                    </div>
                );

            case 'radio':
                return (
                    <div key={field.id} className="mb-6">
                        <label className="block text-gray-700 font-medium mb-3 text-right">
                            {field.label}
                            {field.required && <span className="text-red-500 mr-1">*</span>}
                        </label>
                        <div className="space-y-2">
                            {field.options?.map(option => (
                                <label key={option} className="flex items-center justify-end cursor-pointer">
                                    <span className="text-gray-700 mr-3">{option}</span>
                                    <input
                                        type="radio"
                                        name={field.id}
                                        value={option}
                                        checked={value === option}
                                        onChange={(e) => handleChange(field.id, e.target.value)}
                                        className="w-4 h-4 text-blue-600 focus:ring-blue-500"
                                    />
                                </label>
                            ))}
                        </div>
                        {error && <p className="text-red-500 text-sm mt-1 text-right">{error}</p>}
                    </div>
                );

            case 'multiselect':
                const selectedValues = value ? value.split(',') : [];
                return (
                    <div key={field.id} className="mb-6">
                        <label className="block text-gray-700 font-medium mb-3 text-right">
                            {field.label}
                            {field.required && <span className="text-red-500 mr-1">*</span>}
                        </label>
                        <div className="space-y-2">
                            {field.options?.map(option => (
                                <label key={option} className="flex items-center justify-end cursor-pointer">
                                    <span className="text-gray-700 mr-3">{option}</span>
                                    <input
                                        type="checkbox"
                                        value={option}
                                        checked={selectedValues.includes(option)}
                                        onChange={() => handleMultiSelectChange(field.id, option)}
                                        className="w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                                    />
                                </label>
                            ))}
                        </div>
                        {error && <p className="text-red-500 text-sm mt-1 text-right">{error}</p>}
                    </div>
                );

            default:
                return null;
        }
    };

    return (
        <div className="bg-white rounded-xl shadow-lg p-6 md:p-8">
            <h2 className="text-2xl font-bold text-gray-800 mb-2 text-right">
                {config.title}
            </h2>
            {config.description && (
                <p className="text-gray-600 mb-6 text-right leading-relaxed">
                    {config.description}
                </p>
            )}

            <form onSubmit={handleSubmit}>
                {config.fields.map(field => renderField(field))}

                <div className="flex gap-3 mt-8">
                    <button
                        type="button"
                        onClick={onSkip}
                        className="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                    >
                        رد کردن
                    </button>
                    <button
                        type="submit"
                        className="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                    >
                        ارسال
                    </button>
                </div>
            </form>
        </div>
    );
};

export default SpecialtyForm;
