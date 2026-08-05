export type UserRole = "admin" | "editor" | string;

export type ProfileData = {
  id: string;
  name: string;
  email: string;
  role: UserRole;
  active: boolean;
};

export type AuthMeResponse = {
  user?: ProfileData;
  message?: string;
};

export type UpdateProfileInput = {
  name?: string;
  email?: string;
};

export type UpdateProfileResponse = {
  message?: string;
  user?: ProfileData;
};

export type ChangePasswordInput = {
  currentPassword: string;
  newPassword: string;
};

export type ChangePasswordResponse = {
  message?: string;
};

export type PublicArticle = {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  featuredImageUrl?: string;
  featuredImageCaption?: string | null;
  featuredImagePosition?: string | null;
  createdAt: string;
  scheduledAt?: string | null;
  authorName: string;
  categoryName: string;
  isFeatured?: boolean;
  featuredTypes?: ("none" | "hero" | "headline" | "category_hero" | "breaking" | "las_5_de_x")[];
};

export type PublicHomeResponse = {
  recent?: unknown[];
  featured?: unknown[];
  latest?: unknown[];
};

export type ArticlePreviewData = {
  id?: string;
  title: string;
  excerpt: string;
  content: string;
  featuredImageUrl?: string | null;
  featuredImageCaption?: string | null;
  featuredImagePosition?: string | null;
  tags: string[];
  authorName: string;
  authorAvatarUrl?: string | null;
  authorRole?: string | null;
  categoryName: string;
  publishedAt?: string | null;
};

export type ArticleRecommendation = {
  id: string;
  slug: string;
  title: string;
  excerpt: string;
  featuredImageUrl?: string | null;
  featuredImagePosition?: string | null;
  publishedAt: string;
  tags: string[];
  matchedTags: string[];
  authorName?: string;
  categoryName?: string;
};

export type ArticlePreviewLocationState = {
  article: ArticlePreviewData;
};

export type PublicCategory = {
  id?: string;
  name?: string;
  slug?: string;
  order?: number;
  color?: string | null;
  template?: string;
};

export type PollOption = {
  id: string;
  text: string;
  imageUrl?: string | null;
  votes?: number;
};

export type PollOtherResponse = {
  text: string;
  createdAt: string;
};

export type Poll = {
  id: string;
  title: string;
  description?: string | null;
  imageUrl?: string | null;
  allowMultiple?: boolean;
  allowOther?: boolean;
  active?: boolean;
  order?: number;
  options: PollOption[];
  otherVotes?: number;
  otherResponses?: PollOtherResponse[];
  createdAt?: string;
  updatedAt?: string;
};

