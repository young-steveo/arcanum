<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

enum StatusCode : int
{
    case Continue = 100;
    case SwitchingProtocols = 101;
    case Processing = 102;
    case OK = 200;
    case Created = 201;
    case Accepted = 202;
    case NonAuthoritative = 203;
    case NoContent = 204;
    case ResetContent = 205;
    case PartialContent = 206;
    case MultiStatus = 207;
    case AlreadyReported = 208;
    case MultipleChoices = 300;
    case MovedPermanently = 301;
    case Found = 302;
    case SeeOther = 303;
    case NotModified = 304;
    case UseProxy = 305;
    case SwitchProxy = 306;
    case TemporaryRedirect = 307;
    case PermanentRedirect = 308;
    case BadRequest = 400;
    case Unauthorized = 401;
    case PaymentRequired = 402;
    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;
    case NotAcceptable = 406;
    case ProxyAuthRequired = 407;
    case RequestTimeout = 408;
    case Conflict = 409;
    case Gone = 410;
    case LengthRequired = 411;
    case PreconditionFailed = 412;
    case RequestEntityTooLarge = 413;
    case RequestURITooLarge = 414;
    case UnsupportedMediaType = 415;
    case RangeNotSatisfiable = 416;
    case ExpectationFailed = 417;
    case ImATeapot = 418;
    case UnprocessableEntity = 422;
    case Locked = 423;
    case FailedDependency = 424;
    case UnorderedCollection = 425;
    case UpgradeRequired = 426;
    case PreconditionRequired = 428;
    case TooManyRequests = 429;
    case HeaderFieldsTooLarge = 431;
    case UnavailableForLegal = 451;
    case InternalServerError = 500;
    case NotImplemented = 501;
    case BadGateway = 502;
    case ServiceUnavailable = 503;
    case GatewayTimeout = 504;
    case VersionNotSupported = 505;
    case VariantAlsoNegotiates = 506;
    case InsufficientStorage = 507;
    case LoopDetected = 508;
    case NotExtended = 510;
    case NetworkAuthRequired = 511;

    public function reason(): Phrase
    {
        return match ($this) {
            self::Continue => Phrase::Continue,
            self::SwitchingProtocols => Phrase::SwitchingProtocols,
            self::Processing => Phrase::Processing,
            self::OK => Phrase::OK,
            self::Created => Phrase::Created,
            self::Accepted => Phrase::Accepted,
            self::NonAuthoritative => Phrase::NonAuthoritative,
            self::NoContent => Phrase::NoContent,
            self::ResetContent => Phrase::ResetContent,
            self::PartialContent => Phrase::PartialContent,
            self::MultiStatus => Phrase::MultiStatus,
            self::AlreadyReported => Phrase::AlreadyReported,
            self::MultipleChoices => Phrase::MultipleChoices,
            self::MovedPermanently => Phrase::MovedPermanently,
            self::Found => Phrase::Found,
            self::SeeOther => Phrase::SeeOther,
            self::NotModified => Phrase::NotModified,
            self::UseProxy => Phrase::UseProxy,
            self::SwitchProxy => Phrase::SwitchProxy,
            self::TemporaryRedirect => Phrase::TemporaryRedirect,
            self::PermanentRedirect => Phrase::PermanentRedirect,
            self::BadRequest => Phrase::BadRequest,
            self::Unauthorized => Phrase::Unauthorized,
            self::PaymentRequired => Phrase::PaymentRequired,
            self::Forbidden => Phrase::Forbidden,
            self::NotFound => Phrase::NotFound,
            self::MethodNotAllowed => Phrase::MethodNotAllowed,
            self::NotAcceptable => Phrase::NotAcceptable,
            self::ProxyAuthRequired => Phrase::ProxyAuthRequired,
            self::RequestTimeout => Phrase::RequestTimeout,
            self::Conflict => Phrase::Conflict,
            self::Gone => Phrase::Gone,
            self::LengthRequired => Phrase::LengthRequired,
            self::PreconditionFailed => Phrase::PreconditionFailed,
            self::RequestEntityTooLarge => Phrase::RequestEntityTooLarge,
            self::RequestURITooLarge => Phrase::RequestURITooLarge,
            self::UnsupportedMediaType => Phrase::UnsupportedMediaType,
            self::RangeNotSatisfiable => Phrase::RangeNotSatisfiable,
            self::ExpectationFailed => Phrase::ExpectationFailed,
            self::ImATeapot => Phrase::ImATeapot,
            self::UnprocessableEntity => Phrase::UnprocessableEntity,
            self::Locked => Phrase::Locked,
            self::FailedDependency => Phrase::FailedDependency,
            self::UnorderedCollection => Phrase::UnorderedCollection,
            self::UpgradeRequired => Phrase::UpgradeRequired,
            self::PreconditionRequired => Phrase::PreconditionRequired,
            self::TooManyRequests => Phrase::TooManyRequests,
            self::HeaderFieldsTooLarge => Phrase::HeaderFieldsTooLarge,
            self::UnavailableForLegal => Phrase::UnavailableForLegal,
            self::InternalServerError => Phrase::InternalServerError,
            self::NotImplemented => Phrase::NotImplemented,
            self::BadGateway => Phrase::BadGateway,
            self::ServiceUnavailable => Phrase::ServiceUnavailable,
            self::GatewayTimeout => Phrase::GatewayTimeout,
            self::VersionNotSupported => Phrase::VersionNotSupported,
            self::VariantAlsoNegotiates => Phrase::VariantAlsoNegotiates,
            self::InsufficientStorage => Phrase::InsufficientStorage,
            self::LoopDetected => Phrase::LoopDetected,
            self::NotExtended => Phrase::NotExtended,
            self::NetworkAuthRequired => Phrase::NetworkAuthRequired,
        };
    }
}
