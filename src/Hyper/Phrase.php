<?php

declare(strict_types=1);

namespace Arcanum\Hyper;

enum Phrase : string
{
    case Continue = 'Continue';
    case SwitchingProtocols = 'Switching Protocols';
    case Processing = 'Processing';
    case OK = 'OK';
    case Created = 'Created';
    case Accepted = 'Accepted';
    case NonAuthoritative = 'Non-Authoritative Information';
    case NoContent = 'No Content';
    case ResetContent = 'Reset Content';
    case PartialContent = 'Partial Content';
    case MultiStatus = 'Multi-Status';
    case AlreadyReported = 'Already Reported';
    case MultipleChoices = 'Multiple Choices';
    case MovedPermanently = 'Moved Permanently';
    case Found = 'Found';
    case SeeOther = 'See Other';
    case NotModified = 'Not Modified';
    case UseProxy = 'Use Proxy';
    case SwitchProxy = 'Switch Proxy';
    case TemporaryRedirect = 'Temporary Redirect';
    case PermanentRedirect = 'Permanent Redirect';
    case BadRequest = 'Bad Request';
    case Unauthorized = 'Unauthorized';
    case PaymentRequired = 'Payment Required';
    case Forbidden = 'Forbidden';
    case NotFound = 'Not Found';
    case MethodNotAllowed = 'Method Not Allowed';
    case NotAcceptable = 'Not Acceptable';
    case ProxyAuthRequired = 'Proxy Authentication Required';
    case RequestTimeout = 'Request Timeout';
    case Conflict = 'Conflict';
    case Gone = 'Gone';
    case LengthRequired = 'Length Required';
    case PreconditionFailed = 'Precondition Failed';
    case RequestEntityTooLarge = 'Request Entity Too Large';
    case RequestURITooLarge = 'Request-URI Too Large';
    case UnsupportedMediaType = 'Unsupported Media Type';
    case RangeNotSatisfiable = 'Requested Range Not Satisfiable';
    case ExpectationFailed = 'Expectation Failed';
    case ImATeapot = 'I\'m a teapot';
    case UnprocessableEntity = 'Unprocessable Entity';
    case Locked = 'Locked';
    case FailedDependency = 'Failed Dependency';
    case UnorderedCollection = 'Unordered Collection';
    case UpgradeRequired = 'Upgrade Required';
    case PreconditionRequired = 'Precondition Required';
    case TooManyRequests = 'Too Many Requests';
    case HeaderFieldsTooLarge = 'Request Header Fields Too Large';
    case UnavailableForLegal = 'Unavailable For Legal Reasons';
    case InternalServerError = 'Internal Server Error';
    case NotImplemented = 'Not Implemented';
    case BadGateway = 'Bad Gateway';
    case ServiceUnavailable = 'Service Unavailable';
    case GatewayTimeout = 'Gateway Timeout';
    case VersionNotSupported = 'HTTP Version Not Supported';
    case VariantAlsoNegotiates = 'Variant Also Negotiates';
    case InsufficientStorage = 'Insufficient Storage';
    case LoopDetected = 'Loop Detected';
    case NotExtended = 'Not Extended';
    case NetworkAuthRequired = 'Network Authentication Required';

    public function code(): StatusCode
    {
        return match ($this) {
            self::Continue => StatusCode::Continue,
            self::SwitchingProtocols => StatusCode::SwitchingProtocols,
            self::Processing => StatusCode::Processing,
            self::OK => StatusCode::OK,
            self::Created => StatusCode::Created,
            self::Accepted => StatusCode::Accepted,
            self::NonAuthoritative => StatusCode::NonAuthoritative,
            self::NoContent => StatusCode::NoContent,
            self::ResetContent => StatusCode::ResetContent,
            self::PartialContent => StatusCode::PartialContent,
            self::MultiStatus => StatusCode::MultiStatus,
            self::AlreadyReported => StatusCode::AlreadyReported,
            self::MultipleChoices => StatusCode::MultipleChoices,
            self::MovedPermanently => StatusCode::MovedPermanently,
            self::Found => StatusCode::Found,
            self::SeeOther => StatusCode::SeeOther,
            self::NotModified => StatusCode::NotModified,
            self::UseProxy => StatusCode::UseProxy,
            self::SwitchProxy => StatusCode::SwitchProxy,
            self::TemporaryRedirect => StatusCode::TemporaryRedirect,
            self::PermanentRedirect => StatusCode::PermanentRedirect,
            self::BadRequest => StatusCode::BadRequest,
            self::Unauthorized => StatusCode::Unauthorized,
            self::PaymentRequired => StatusCode::PaymentRequired,
            self::Forbidden => StatusCode::Forbidden,
            self::NotFound => StatusCode::NotFound,
            self::MethodNotAllowed => StatusCode::MethodNotAllowed,
            self::NotAcceptable => StatusCode::NotAcceptable,
            self::ProxyAuthRequired => StatusCode::ProxyAuthRequired,
            self::RequestTimeout => StatusCode::RequestTimeout,
            self::Conflict => StatusCode::Conflict,
            self::Gone => StatusCode::Gone,
            self::LengthRequired => StatusCode::LengthRequired,
            self::PreconditionFailed => StatusCode::PreconditionFailed,
            self::RequestEntityTooLarge => StatusCode::RequestEntityTooLarge,
            self::RequestURITooLarge => StatusCode::RequestURITooLarge,
            self::UnsupportedMediaType => StatusCode::UnsupportedMediaType,
            self::RangeNotSatisfiable => StatusCode::RangeNotSatisfiable,
            self::ExpectationFailed => StatusCode::ExpectationFailed,
            self::ImATeapot => StatusCode::ImATeapot,
            self::UnprocessableEntity => StatusCode::UnprocessableEntity,
            self::Locked => StatusCode::Locked,
            self::FailedDependency => StatusCode::FailedDependency,
            self::UnorderedCollection => StatusCode::UnorderedCollection,
            self::UpgradeRequired => StatusCode::UpgradeRequired,
            self::PreconditionRequired => StatusCode::PreconditionRequired,
            self::TooManyRequests => StatusCode::TooManyRequests,
            self::HeaderFieldsTooLarge => StatusCode::HeaderFieldsTooLarge,
            self::UnavailableForLegal => StatusCode::UnavailableForLegal,
            self::InternalServerError => StatusCode::InternalServerError,
            self::NotImplemented => StatusCode::NotImplemented,
            self::BadGateway => StatusCode::BadGateway,
            self::ServiceUnavailable => StatusCode::ServiceUnavailable,
            self::GatewayTimeout => StatusCode::GatewayTimeout,
            self::VersionNotSupported => StatusCode::VersionNotSupported,
            self::VariantAlsoNegotiates => StatusCode::VariantAlsoNegotiates,
            self::InsufficientStorage => StatusCode::InsufficientStorage,
            self::LoopDetected => StatusCode::LoopDetected,
            self::NotExtended => StatusCode::NotExtended,
            self::NetworkAuthRequired => StatusCode::NetworkAuthRequired,
        };
    }
}
