//////////////////////////////////////////////////////////////////
///////////////////////// PLATFORM CHECKS ////////////////////////
//////////////////////////////////////////////////////////////////

// https://stackoverflow.com/questions/8666378/detect-windows-or-linux-in-c-c#comment127725511_33088568
#if defined(WIN32) || defined(_WIN32) || defined(__WIN32) || defined(__WIN32__)
#include <windows.h>
#define WINDOWS_OS

//https://www.geeksforgeeks.org/how-to-detect-operating-system-through-a-c-program/
#elif defined(linux) || defined(__linux__)
#include <unistd.h>
#define LINUX_OS

#else
#error This platform is not supported yet.
#endif // platform checks



//////////////////////////////////////////////////////////////////
//////////////////////// ADMIN/SUDO CHECK ////////////////////////
//////////////////////////////////////////////////////////////////

#ifdef WINDOWS_OS
// Source: https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/nf-securitybaseapi-checktokenmembership
bool _isElevated()
/*++ 
Routine Description: This routine returns TRUE if the caller's
process is a member of the Administrators local group. Caller is NOT
expected to be impersonating anyone and is expected to be able to
open its own process and process token. 
Arguments: None. 
Return Value: 
TRUE - Caller has Administrators local group. 
FALSE - Caller does not have Administrators local group. --
*/ 
{
    BOOL b;
    SID_IDENTIFIER_AUTHORITY NtAuthority = SECURITY_NT_AUTHORITY;
    PSID AdministratorsGroup; 
    b = AllocateAndInitializeSid(
        &NtAuthority,
        2,
        SECURITY_BUILTIN_DOMAIN_RID,
        DOMAIN_ALIAS_RID_ADMINS,
        0, 0, 0, 0, 0, 0,
        &AdministratorsGroup); 
    if(b) 
    {
        if (!CheckTokenMembership( NULL, AdministratorsGroup, &b)) 
        {
            b = FALSE;
        } 
        FreeSid(AdministratorsGroup); 
    }

    return b;
}

#elif defined(LINUX_OS)
bool _isElevated() {
    // https://stackoverflow.com/a/4159919/13680015
    return geteuid() == 0;
}
#endif


//////////////////////////////////////////////////////////////////
/////////////////////// PROGRAM FUNCTIONS ////////////////////////
//////////////////////////////////////////////////////////////////

#include <iostream>
#include <string>
#include <cctype>
#include <vector>
#include <functional>
#include <map>
#include <algorithm>
#include <exception>
#include <type_traits>

class ReadableException : public std::exception {
private:
    std::string const m_message;

public:
    ReadableException(std::string message) : std::exception(), m_message{ message } {}

    virtual char const* what() const noexcept override {
        return m_message.c_str();
    }
};

class MalformedDefaultAction : public ReadableException {
public:
    MalformedDefaultAction() : ReadableException("Default action must take no mandatory arguments") {}
};

class MalformedParameter : public ReadableException {
public:
    using ReadableException::ReadableException;
};

std::string implode(std::string const& delimiter, std::vector<std::string> const& strings) {
    int n = (int)strings.size();
    std::string result{ };
    for (int i = 0; i < n; ++i) {
        if (i != 0) result += delimiter;
        result += strings[i];
    }
    return result;
}

template<typename... Ts, typename std::enable_if<std::is_same<Ts..., std::string>::value, bool>::type = true>
std::string implode(std::string const& delimiter, std::string const& first, Ts const& ...more) {
    std::vector<std::string> v{ first, more... };
    return implode(delimiter, v);
}

struct ActionCallbackParameter {
    std::string shortName;
    std::string longName;
    bool required;
    std::string description;
};

using ActionCallbackParameterList = std::vector<ActionCallbackParameter>;

using ActionCallbackArguments = std::map<std::string, std::string>; // map from parameter->longName to argument value

using ActionCallback = std::function<void (ActionCallbackArguments const&)>;

struct Action {
    std::string name;
    std::string shortDescription;
    std::string longDescription;
    ActionCallback callback;
    ActionCallbackParameterList parameterList;

    Action(Action const&) = default;

    Action(std::string _name, std::string _shortDescription, std::string _longDescription, ActionCallback _callback, ActionCallbackParameterList _parameterList)
        : name{ _name },
        shortDescription{ _shortDescription },
        longDescription{ _longDescription },
        callback{ _callback },
        parameterList{ _parameterList }
    {
        for (auto const& p : parameterList) {
            if (p.shortName.empty() || p.longName.empty()) {
                throw MalformedParameter("A parameter must have both its short name and long name present");
            }
            else if (!isalpha(p.shortName[0]) || !isalpha(p.longName[0])) {
                throw MalformedParameter("A parameter's short name and long name must start with an alphabetical letter");
            }
        }

        std::sort(parameterList.begin(), parameterList.end(), [](ActionCallbackParameter const& a, ActionCallbackParameter const& b) {
            // Keep the original insertion order ; just move the required parameters to the front.
            if (a.required != b.required) {
                return a.required;
            }
            return false;
        });
    }
};

class CommandLineArgumentParser {
private:
    std::vector<std::string> const m_args;

    std::vector<Action> m_actions;

    Action const m_defaultAction;

    static std::vector<std::string> convertArgcArgvToArgs(int argc, char** argv) {
        std::vector<std::string> args(argc - 1);
        for (int i = 1; i < argc; ++i) {
            args[i - 1] = argv[i];
        }
        return args;
    }

    std::string getParameterNames(ActionCallbackParameter const& p) {
        if (p.shortName.empty()) {
            return "--" + p.longName;
        }
        else {
            return implode(", ", "-" + p.shortName, "--" + p.longName);
        }
    }

public:
    CommandLineArgumentParser(int argc, char** argv, Action const& defaultAction)
        : m_args{ convertArgcArgvToArgs(argc, argv) },
        m_defaultAction{ defaultAction }
    {
        if (!defaultAction.parameterList.empty()) {
            throw MalformedDefaultAction();
        }
        m_actions.push_back(defaultAction);
    }

    void addAction(Action action, bool setAsDefault = true) {
        m_actions.push_back(action);
        std::sort(m_actions.begin(), m_actions.end(), [](Action const& a, Action const& b) {
            return a.name.compare(b.name) < 0;
        });
    }

    void displayActions() {
        std::cout << "Syntax: ./app [actionName [requiredParameter1Value, ...] [optionalParameter1Name optionalParameter1Value, ...]]" << std::endl;
        std::cout << "Actions:" << std::endl << std::endl;

        for (auto const& action : m_actions) {
            std::cout << (action.name.empty() ? "<left empty>" : action.name + "\t") << "\t\t\t" << action.shortDescription << std::endl;
            for (auto const& p : action.parameterList) {
                std::cout << "\t" << getParameterNames(p) << "\t\t" << p.description << std::endl;
            }
            std::cout << std::endl;
        }
    }

    void execute(Action const& action, ActionCallbackArguments const& args) {
        action.callback(args);
    }

    void parseAndExecute() {
        if (m_args.empty()) {
            return execute(m_defaultAction, {});
        }
        std::string actionName = m_args[0];
        auto actionIter = std::find_if(m_actions.begin(), m_actions.end(), [&actionName](Action const& action) {
            return action.name == actionName;
        });

        if (actionIter == m_actions.end()) {
            std::cout << "No action named '" << actionName << "'" << std::endl;
            return;
        }

        Action const& action = *actionIter;
        ActionCallbackParameterList remainingParameters = action.parameterList; // deep copy
        auto rpIter = remainingParameters.begin();

        bool openingParameter = false;
        ActionCallbackArguments args{};

        for (int i = 1; i < (int)m_args.size(); ++i) {
            std::string const& a = m_args[i];

            if (remainingParameters.empty()) {
                std::cout << "Redundant arguments passed (from '" << a << "' to the end of line)" << std::endl;
                return;
            }

            if (!openingParameter) {
                if (a.size() >= 3 && a.substr(0, 2) == "--" && isalpha(a[2])) {
                    openingParameter = true;
                    std::string longParameterName = a.substr(2);
                    rpIter = std::find_if(remainingParameters.begin(), remainingParameters.end(), [&longParameterName](ActionCallbackParameter const& p) {
                        return p.longName == longParameterName;
                    });
                    if (rpIter == remainingParameters.end()) {
                        std::cout << "No parameter of action '" << action.name << "' named '--" << longParameterName << "'" << std::endl;
                        return;
                    }
                }
                else if (a.size() >= 2 && a[0] == '-' && isalpha(a[1])) {
                    openingParameter = true;
                    std::string shortParameterName = a.substr(1);
                    rpIter = std::find_if(remainingParameters.begin(), remainingParameters.end(), [&shortParameterName](ActionCallbackParameter const& p) {
                        return p.shortName == shortParameterName;
                    });
                    if (rpIter == remainingParameters.end()) {
                        std::cout << "While executing action '" << action.name << "': Parameter '-" << shortParameterName << "' duplicate or inexistent" << std::endl;
                        return;
                    }
                }
                else {
                    // Positional argument => get the first parameter remaining
                    rpIter = remainingParameters.begin();
                    openingParameter = true;
                    --i;
                }
            }
            else {
                // openingParameter
                args[rpIter->longName] = a;
                // std::cout << "PARSED PARAMETER '" << rpIter->longName << "' AS '" << a << "'" << std::endl;
                remainingParameters.erase(rpIter);
                openingParameter = false;
            }
        }

        if (openingParameter) {
            std::cout << "Expecting argument for parameter '" << rpIter->longName << "'" << std::endl;
            return;
        }

        if (!remainingParameters.empty()) {
            rpIter = std::find_if(remainingParameters.begin(), remainingParameters.end(), [](ActionCallbackParameter const& p) {
                return p.required == true;
            });
            if (rpIter != remainingParameters.end()) {
                std::cout << "Missing argument for required parameter '" << rpIter->longName << "' (and maybe more)" << std::endl;
                return;
            }
        }

        return execute(action, args);
    }
};

using namespace std::placeholders; // for _1, _2, _3...

class Application {
#define ac(memberMethodName) (std::bind(&Application::memberMethodName, this, _1))
private:
    bool const m_isElevated;
    CommandLineArgumentParser clap;

public:
    Application(int argc, char** argv)
        : m_isElevated{ _isElevated() },
        clap(argc, argv, Action("", "Same as help", "", ac(displayHelp), {}))
    {
        clap.addAction(Action(
            "help", "Show how to use this tool.", "",
            ac(displayHelp),
            {}
        ));

        clap.addAction(Action(
            "add", "Add two to three numbers.", "",
            ac(add),
            {
                { "f", "first", true, "The first number" },
                { "s", "second", true, "The second number" },
                { "t", "third", false, "The third number (optionally)" }
            }
        ));
    }

    void displayHelp(ActionCallbackArguments const& args) {
        std::cout << "Rhyme Project Manager - 0.0.1" << std::endl;
        clap.displayActions();
    }

    void add(ActionCallbackArguments const& args) {
        double d1 = std::stod(args.at("first"));
        double d2 = std::stod(args.at("second"));
        double d3 = (args.count("third") ? std::stod(args.at("third")) : 0);
        std::cout << "Result: " << (d1 + d2 + d3) << std::endl;
    }

    void run() {
        clap.parseAndExecute();
    }
#undef ac
};

//////////////////////////////////////////////////////////////////
////////////////////////// MAIN PROGRAM //////////////////////////
//////////////////////////////////////////////////////////////////

int main(int argc, char** argv) {
    Application app(argc, argv);
    app.run();
    return 0;
}
